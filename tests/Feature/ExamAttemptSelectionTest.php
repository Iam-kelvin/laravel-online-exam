<?php

namespace Tests\Feature;

use App\Models\ExamAttempt;
use App\Models\ExamPreset;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExamAttemptSelectionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_exam_autofills_short_subjects_from_other_selected_subjects(): void
    {
        $user = User::factory()->create();
        $suffix = uniqid();
        $english = Subject::create(['name' => "English Test {$suffix}", 'slug' => "english-test-{$suffix}", 'active' => true]);
        $math = Subject::create(['name' => "Math Test {$suffix}", 'slug' => "math-test-{$suffix}", 'active' => true]);
        $preset = ExamPreset::create([
            'label' => '4 Questions',
            'question_count' => 4,
            'duration_seconds' => 240,
            'active' => true,
        ]);

        $this->createQuestion($english, 1);

        foreach (range(1, 5) as $index) {
            $this->createQuestion($math, $index);
        }

        $response = $this->actingAs($user)->post(route('exam.store'), [
            'subject_ids' => [$english->id, $math->id],
            'exam_preset_id' => $preset->id,
        ]);

        $attempt = ExamAttempt::where('user_id', $user->id)->latest()->first();

        $response->assertRedirect(route('exam.take', $attempt));
        $this->assertSame(4, $attempt->question_count);
        $this->assertSame(4, $attempt->requested_question_count);

        $subjectCounts = $attempt->questions()
            ->select('subject_id', DB::raw('count(*) as total'))
            ->groupBy('subject_id')
            ->pluck('total', 'subject_id');

        $this->assertSame(1, (int) $subjectCounts[$english->id]);
        $this->assertSame(3, (int) $subjectCounts[$math->id]);
    }

    private function createQuestion(Subject $subject, int $index): Question
    {
        return Question::create([
            'subject_id' => $subject->id,
            'question' => "Question {$subject->name} {$index}",
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'answer' => 'option_a',
        ]);
    }
}
