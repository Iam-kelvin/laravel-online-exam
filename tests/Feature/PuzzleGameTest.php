<?php

namespace Tests\Feature;

use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PuzzleGameTest extends TestCase
{
    use DatabaseTransactions;

    private function game(string $game = 'word-scramble', int $duration = 60): ExamAttempt
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('games.store', $game), ['duration_seconds' => $duration])->assertSessionHasNoErrors();

        return $user->examAttempts()->firstOrFail();
    }

    public function test_games_have_their_own_authenticated_section_and_duration_limits(): void
    {
        $this->get(route('games.index'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('games.index'))->assertOk()->assertSee('Word Scramble')->assertSee('Picture Puzzles');
        $this->get(route('games.start', 'word-scramble'))->assertOk()->assertSee('60 seconds');
        $this->get(route('games.start', 'unknown'))->assertNotFound();
        $this->post(route('games.store', 'unknown'), ['duration_seconds' => 60])->assertNotFound();
        foreach ([0, 16, 181, 600, 'bad'] as $duration) {
            $this->post(route('games.store', 'word-scramble'), ['duration_seconds' => $duration])->assertSessionHasErrors('duration_seconds');
        }
        $this->assertSame(0, auth()->user()->examAttempts()->count());
    }

    public function test_word_games_save_scrambled_prompts_and_do_not_reveal_answers(): void
    {
        $attempt = $this->game('word-scramble', 15);
        $this->assertTrue($attempt->isGame());
        $this->assertTrue($attempt->isRush());
        $this->assertSame('Word Scramble', $attempt->examName());
        $this->assertSame(15, $attempt->duration_seconds);
        $this->assertSame(0, $attempt->subjects()->count());
        foreach ($attempt->questions as $question) {
            $letters = str_split(strtolower($question->prompt_data['letters']));
            $answer = str_split($question->correct_answer);
            sort($letters);
            sort($answer);
            $this->assertSame($answer, $letters);
            $this->assertNotSame(strtoupper($question->correct_answer), $question->prompt_data['letters']);
        }
        $this->get(route('exam.take', $attempt))->assertOk()->assertSee('Your word')->assertSee('Skip')->assertDontSee('correct_answer')->assertDontSee('answer_aliases');
        $this->get(route('exam.results'))->assertOk()->assertSee('Word Scramble');
    }

    public function test_typed_answers_are_normalised_locked_ordered_and_reviewed_only_if_attempted(): void
    {
        $attempt = $this->game();
        $first = $attempt->questions()->first();
        $next = $attempt->questions()->skip(1)->first();
        $this->postJson(route('exam.answer', [$attempt, $first]), ['answer' => ''])->assertUnprocessable();
        $this->postJson(route('exam.answer', [$attempt, $first]), ['answer' => str_repeat('x', 121)])->assertUnprocessable();
        $this->postJson(route('exam.answer', [$attempt, $next]), ['skip' => true])->assertStatus(409);
        $this->postJson(route('exam.answer', [$attempt, $first]), ['answer' => '  ' . strtoupper($first->correct_answer) . '!  '])
            ->assertOk()->assertJson(['is_correct' => true, 'answered' => 1, 'score' => 1]);
        $this->postJson(route('exam.answer', [$attempt, $first]), ['answer' => 'wrong'])
            ->assertOk()->assertJson(['is_correct' => true, 'answered' => 1, 'score' => 1]);
        $this->postJson(route('exam.answer', [$attempt, $next]), ['skip' => true])
            ->assertOk()->assertJson(['is_correct' => false, 'selected_answer' => '[Skipped]', 'answered' => 2, 'score' => 1]);
        $this->post(route('exam.submit', $attempt), ['answers' => [$next->id => $next->correct_answer]])->assertRedirect(route('exam.review', $attempt));
        $this->get(route('exam.review', $attempt))->assertOk()->assertSee('Word Scramble Results')
            ->assertSee('50% accuracy')->assertSee('Skipped')->assertSee('Drop feedback')
            ->assertViewHas('attempt', fn ($review) => $review->questions->count() === 2);
    }

    public function test_picture_puzzles_use_existing_images_and_accept_phrase_variants(): void
    {
        $attempt = $this->game('picture-puzzles');
        $first = $attempt->questions()->first();
        $this->get(route('exam.take', $attempt))->assertOk()->assertSee('Your phrase')->assertSee('puzzles/rebus/')
            ->assertDontSee($first->correct_answer);
        foreach ($attempt->questions as $question) {
            $this->assertFileExists(public_path($question->prompt_data['image']));
        }
        $first->update(['correct_answer' => 'Reading between the lines', 'answer_aliases' => ['Read between the lines']]);
        $this->postJson(route('exam.answer', [$attempt, $first]), ['answer' => 'READ between lines!'])
            ->assertOk()->assertJson(['is_correct' => true, 'correct_answer' => $first->correct_answer]);
        $this->post(route('exam.submit', $attempt))->assertRedirect(route('exam.review', $attempt));
        $this->get(route('reports.take', $attempt->share_token))->assertRedirect(route('games.start', [
            'game' => 'picture-puzzles', 'duration_seconds' => 60,
        ]));
    }

    public function test_game_answers_enforce_ownership_and_the_server_deadline(): void
    {
        $attempt = $this->game();
        $first = $attempt->questions()->first();
        $this->actingAs(User::factory()->create())->postJson(route('exam.answer', [$attempt, $first]), ['skip' => true])->assertForbidden();
        $this->actingAs($attempt->user)->postJson(route('exam.answer', [$attempt, $first]), ['answer' => 'definitely wrong'])->assertOk()->assertJson(['is_correct' => false]);
        $this->travel(61)->seconds();
        $next = $attempt->questions()->skip(1)->first();
        $this->postJson(route('exam.answer', [$attempt, $next]), ['answer' => $next->correct_answer])->assertStatus(410);
        $this->assertNull($next->fresh()->selected_answer);
        $this->assertNotNull($attempt->fresh()->submitted_at);
        $this->assertSame(0, $attempt->fresh()->score);
    }

    public function test_feedback_link_opens_a_ready_community_form_and_feedback_can_be_posted(): void
    {
        $attempt = $this->game();
        $this->post(route('exam.submit', $attempt));
        $this->get(route('exam.review', $attempt))->assertSee('community?feedback=Word%20Scramble');
        $this->get(route('community.index', ['feedback' => 'Word Scramble']))->assertOk()->assertSee('Feedback: Word Scramble')->assertSee('community-post-form');
        $this->post(route('community.store'), [
            'title' => 'Feedback: Word Scramble', 'category' => 'feedback', 'body' => 'More difficult words would be fun.',
        ])->assertRedirect(route('community.index'));
        $this->assertDatabaseHas('community_posts', ['category' => 'feedback', 'user_id' => $attempt->user_id]);
        $this->get('/community?feedback[]=bad')->assertOk();
    }
}
