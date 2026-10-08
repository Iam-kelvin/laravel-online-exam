<?php

namespace Tests\Feature;

use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RushExamTest extends TestCase
{
    use DatabaseTransactions;

    public function test_practice_allows_one_hundred_questions_in_one_minute_without_a_preset(): void
    {
        $user = User::factory()->create();
        $subject = $this->subject(100);
        $this->actingAs($user)->post(route('exam.store'), [
            'mode' => 'standard',
            'subject_ids' => [$subject->id],
            'question_count' => 100,
            'duration_value' => 1,
            'duration_unit' => 'minutes',
        ])->assertSessionHasNoErrors();

        $attempt = $user->examAttempts()->firstOrFail();
        $this->assertSame(100, $attempt->question_count);
        $this->assertSame(60, $attempt->duration_seconds);
        $this->assertSame(60, $attempt->started_at->diffInSeconds($attempt->ends_at));
        $this->assertNull($attempt->exam_preset_id);
        $this->get(route('exam.take', $attempt))->assertOk()->assertDontSee('Rush exam');
        $this->post(route('exam.submit', $attempt), [
            'answers' => [$attempt->questions()->first()->id => 'option_a'],
        ])->assertRedirect(route('exam.review', $attempt));
        $this->assertSame(1, $attempt->fresh()->score);
    }

    public function test_rush_uses_one_subject_and_custom_duration(): void
    {
        $attempt = $this->rush(3, 30);
        $this->assertSame('rush', $attempt->mode);
        $this->assertSame(30, $attempt->duration_seconds);
        $this->assertSame(3, $attempt->question_count);
        $this->assertSame(1, $attempt->subjects()->count());
        $this->get(route('exam.take', $attempt))->assertOk()->assertSee('Rush exam')->assertSee('Sound on');
    }

    public function test_rush_rejects_multiple_or_inactive_subjects(): void
    {
        $user = User::factory()->create();
        $first = $this->subject();
        $second = $this->subject();
        $this->actingAs($user)->post(route('exam.store'), [
            'mode' => 'rush', 'subject_ids' => [$first->id, $second->id], 'duration_seconds' => 60,
        ])->assertSessionHasErrors('subject_ids');
        $first->update(['active' => false]);
        $this->post(route('exam.store'), [
            'mode' => 'rush', 'subject_ids' => [$first->id], 'duration_seconds' => 60,
        ])->assertSessionHasErrors('subject_ids.0');
        $this->assertSame(0, $user->examAttempts()->count());
    }

    public function test_invalid_counts_and_durations_do_not_create_an_attempt(): void
    {
        $user = User::factory()->create();
        $subject = $this->subject();
        foreach ([0, -1, 86401, 'bad'] as $duration) {
            $this->actingAs($user)->post(route('exam.store'), [
                'mode' => 'standard', 'subject_ids' => [$subject->id],
                'question_count' => 10, 'duration_seconds' => $duration,
            ])->assertSessionHasErrors('duration_seconds');
        }
        $this->post(route('exam.store'), [
            'mode' => 'standard', 'subject_ids' => [$subject->id],
            'question_count' => 0, 'duration_seconds' => 60,
        ])->assertSessionHasErrors('question_count');
        $this->assertSame(0, $user->examAttempts()->count());
    }

    public function test_answers_are_marked_immediately_and_cannot_be_changed(): void
    {
        $attempt = $this->rush();
        $question = $attempt->questions()->first();
        $this->postJson(route('exam.answer', [$attempt, $question]), ['answer' => 'option_b'])
            ->assertOk()->assertJson([
                'is_correct' => false, 'selected_answer' => 'option_b',
                'correct_answer' => 'option_a', 'answered' => 1, 'score' => 0, 'finished' => false,
            ]);
        $this->postJson(route('exam.answer', [$attempt, $question]), ['answer' => 'option_a'])
            ->assertOk()->assertJson(['is_correct' => false, 'selected_answer' => 'option_b', 'answered' => 1, 'score' => 0]);
        $this->assertSame('option_b', $question->fresh()->selected_answer);

        $next = $attempt->questions()->skip(1)->first();
        $this->postJson(route('exam.answer', [$attempt, $next]), ['answer' => 'option_a'])
            ->assertOk()->assertJson(['is_correct' => true, 'answered' => 2, 'score' => 1]);
    }

    public function test_rush_finishes_when_the_subject_bank_is_cleared(): void
    {
        $attempt = $this->rush(1);
        $question = $attempt->questions()->first();
        $this->postJson(route('exam.answer', [$attempt, $question]), ['answer' => 'option_a'])
            ->assertOk()->assertJson(['is_correct' => true, 'finished' => true, 'score' => 1]);
        $this->assertNotNull($attempt->fresh()->submitted_at);
        $this->assertSame(1, $attempt->fresh()->score);
        $this->get(route('exam.take', $attempt))->assertRedirect(route('exam.review', $attempt));
        $this->get(route('exam.review', $attempt))->assertOk()->assertSee('Rush Results')->assertSee('100% accuracy');
    }

    public function test_rush_rejects_answers_after_the_timer_ends_and_keeps_saved_answers(): void
    {
        $attempt = $this->rush();
        $questions = $attempt->questions;
        $this->postJson(route('exam.answer', [$attempt, $questions[0]]), ['answer' => 'option_a'])->assertOk();
        $this->travel(61)->seconds();
        $this->postJson(route('exam.answer', [$attempt, $questions[1]]), ['answer' => 'option_a'])
            ->assertStatus(410)->assertJson(['review_url' => route('exam.review', $attempt)]);
        $this->assertSame(1, $attempt->fresh()->score);
        $this->assertSame(60, $attempt->fresh()->time_used_seconds);
        $this->assertNull($questions[1]->fresh()->selected_answer);
        $this->assertNotNull($attempt->fresh()->submitted_at);
    }

    public function test_finishing_rush_ignores_forged_form_answers(): void
    {
        $attempt = $this->rush();
        $questions = $attempt->questions;
        $this->postJson(route('exam.answer', [$attempt, $questions[0]]), ['answer' => 'option_a'])->assertOk();
        $this->post(route('exam.submit', $attempt), [
            'answers' => $questions->mapWithKeys(fn ($question) => [$question->id => 'option_a'])->all(),
        ])->assertRedirect(route('exam.review', $attempt));
        $this->assertSame(1, $attempt->fresh()->score);
        $this->assertNull($questions[1]->fresh()->selected_answer);
        $this->post(route('exam.submit', $attempt))->assertRedirect(route('exam.review', $attempt));
        $this->assertSame(1, $attempt->fresh()->score);
    }

    public function test_rush_answers_require_owner_current_question_and_valid_option(): void
    {
        $attempt = $this->rush();
        $question = $attempt->questions()->first();
        $second = $attempt->questions()->skip(1)->first();
        $this->postJson(route('exam.answer', [$attempt, $question]), ['answer' => 'invalid'])->assertUnprocessable();
        $this->postJson(route('exam.answer', [$attempt, $second]), ['answer' => 'option_a'])->assertStatus(409);
        $this->actingAs(User::factory()->create())
            ->postJson(route('exam.answer', [$attempt, $question]), ['answer' => 'option_a'])->assertForbidden();
        $this->assertNull($question->fresh()->selected_answer);
    }

    public function test_rush_answer_endpoint_rejects_questions_from_another_attempt_and_practice_mode(): void
    {
        $first = $this->rush();
        $second = $this->rush();
        $this->actingAs($first->user)
            ->postJson(route('exam.answer', [$first, $second->questions()->first()]), ['answer' => 'option_a'])->assertNotFound();
        $first->update(['mode' => 'standard']);
        $this->postJson(route('exam.answer', [$first, $first->questions()->first()]), ['answer' => 'option_a'])->assertNotFound();
    }

    public function test_refresh_keeps_marked_answers_and_expired_rush_redirects_to_results(): void
    {
        $attempt = $this->rush();
        $question = $attempt->questions()->first();
        $this->postJson(route('exam.answer', [$attempt, $question]), ['answer' => 'option_a'])->assertOk();
        $this->get(route('exam.take', $attempt))->assertOk()
            ->assertDontSee('data-question-id="' . $question->id . '"', false)
            ->assertSee('<span id="rushCorrect">1</span>', false);
        $this->travel(61)->seconds();
        $this->get(route('exam.take', $attempt))->assertRedirect(route('exam.review', $attempt));
        $this->assertSame(1, $attempt->fresh()->score);
    }

    public function test_shared_combo_keeps_custom_count_duration_and_mode(): void
    {
        $attempt = $this->rush(1, 15);
        $this->postJson(route('exam.answer', [$attempt, $attempt->questions()->first()]), ['answer' => 'option_a'])->assertOk();
        $this->get(route('reports.show', $attempt->share_token))->assertOk()->assertSee('15s Rush');
        $this->get(route('reports.take', $attempt->share_token))->assertRedirect(route('exam.start', [
            'subject_ids' => $attempt->subjects->pluck('id')->all(), 'mode' => 'rush',
            'question_count' => 1, 'duration_seconds' => 15, 'combo' => $attempt->share_token,
        ]));
    }

    public function test_rush_duration_is_limited_to_fifteen_second_steps_up_to_three_minutes(): void
    {
        $user = User::factory()->create();
        $subject = $this->subject();
        foreach ([0, 14, 16, 25, 181, 600] as $duration) {
            $this->actingAs($user)->post(route('exam.store'), [
                'mode' => 'rush', 'subject_ids' => [$subject->id], 'duration_seconds' => $duration,
            ])->assertSessionHasErrors('duration_seconds');
        }
        $this->assertSame(0, $user->examAttempts()->count());
        foreach ([15, 30, 60, 105, 180] as $duration) {
            $this->post(route('exam.store'), [
                'mode' => 'rush', 'subject_ids' => [$subject->id], 'duration_seconds' => $duration,
            ])->assertSessionHasNoErrors();
        }
        $this->assertSame(5, $user->examAttempts()->count());
    }

    public function test_rush_review_only_contains_answered_questions(): void
    {
        $attempt = $this->rush(12);
        $questions = $attempt->questions;
        foreach ($questions->take(2) as $question) {
            $this->postJson(route('exam.answer', [$attempt, $question]), ['answer' => 'option_a'])->assertOk();
        }
        $this->post(route('exam.submit', $attempt))->assertRedirect(route('exam.review', $attempt));
        $response = $this->get(route('exam.review', $attempt))->assertOk();
        $reviewQuestions = $response->viewData('attempt')->questions;
        $this->assertSame(2, $reviewQuestions->count());
        $this->assertEquals($questions->take(2)->pluck('id')->all(), $reviewQuestions->pluck('id')->all());
        foreach ($questions->skip(2) as $question) {
            $response->assertDontSee('<p class="mb-3">' . e($question->question_text) . '</p>', false);
        }
        $this->assertSame(12, $attempt->questions()->count());

        // Practice reviews still include unanswered questions.
        $attempt->update(['mode' => 'standard']);
        $this->assertSame(12, $this->get(route('exam.review', $attempt))->viewData('attempt')->questions->count());
    }

    public function test_intro_and_result_clips_are_not_used_for_wrong_answers(): void
    {
        $attempt = $this->rush();
        $response = $this->get(route('exam.take', $attempt))->assertOk();
        $this->assertStringContainsString('/intro/lets-have-it-lets-have-it.mp3', $response->viewData('introSound'));
        $wrongSounds = $response->viewData('wrongSounds');
        foreach (['faaaa.mp3', 'i-pour-you-spit.mp3', 'shocked-sound-effect.mp3'] as $filename) {
            $this->assertContains(asset('sounds/rush/wrong/' . $filename), $wrongSounds);
        }
        foreach ($wrongSounds as $url) {
            $this->assertStringContainsString('/wrong/', $url);
            $this->assertStringNotContainsString('lets-have-it', $url);
            $this->assertStringNotContainsString('studio-audience', $url);
            $this->assertStringNotContainsString('you-dey-go-na', $url);
        }
    }

    public function test_result_reactions_use_accuracy_of_answered_questions(): void
    {
        foreach ([[10, 6], [20, 11], [10, 5], [10, 4], [0, 0]] as [$answered, $correct]) {
            $attempt = $this->rush($answered + 3, 180);
            foreach ($attempt->questions->take($answered) as $index => $question) {
                $this->postJson(route('exam.answer', [$attempt, $question]), [
                    'answer' => $index < $correct ? 'option_a' : 'option_b',
                ])->assertOk();
            }
            $this->post(route('exam.submit', $attempt))->assertRedirect(route('exam.review', $attempt));
            $response = $this->get(route('exam.review', $attempt))->assertOk();
            $expected = $answered > 0 && $correct * 2 > $answered
                ? 'studio-audience-awwww-sound-fx.mp3' : 'you-dey-go-na.mp3';
            $this->assertStringEndsWith($expected, $response->viewData('resultSound'));
            $this->assertSame($answered, $response->viewData('attempt')->questions->count());
            if ($answered === 0) $response->assertSee('You did not answer any questions in this Rush.');
        }
    }

    private function rush(int $count = 3, int $duration = 60): ExamAttempt
    {
        $user = User::factory()->create();
        $subject = $this->subject($count);
        $this->actingAs($user)->post(route('exam.store'), [
            'mode' => 'rush', 'subject_ids' => [$subject->id], 'duration_seconds' => $duration,
        ])->assertSessionHasNoErrors();

        return $user->examAttempts()->firstOrFail();
    }

    private function subject(int $count = 3): Subject
    {
        $suffix = uniqid();
        $subject = Subject::create(['name' => "Rush Test {$suffix}", 'slug' => "rush-test-{$suffix}", 'active' => true]);
        for ($index = 1; $index <= $count; $index++) {
            Question::create([
                'subject_id' => $subject->id, 'question' => "Rush question {$index}",
                'option_a' => 'Right answer', 'option_b' => 'Wrong answer',
                'option_c' => 'Another option', 'option_d' => 'Last option', 'answer' => 'option_a',
            ]);
        }

        return $subject;
    }
}
