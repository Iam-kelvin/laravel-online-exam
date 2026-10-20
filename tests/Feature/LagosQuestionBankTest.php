<?php

namespace Tests\Feature;

use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use App\Services\LagosQuestionBank;
use Database\Seeders\LagosStreetKnowledgeSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LagosQuestionBankTest extends TestCase
{
    use DatabaseTransactions;

    public function test_refresh_retires_generated_prompts_preserves_custom_questions_and_saved_reviews_and_is_repeatable(): void
    {
        $subject = Subject::firstOrCreate(['name' => 'Lagos Street Knowledge'], [
            'slug' => 'lagos-street-knowledge', 'bank_type' => 'challenge', 'active' => true,
        ]);
        $row = ['option_a' => 'Ikeja', 'option_b' => 'Ikoyi', 'option_c' => 'Epe', 'option_d' => 'Yaba', 'answer' => 'option_a'];
        $old = $subject->questions()->create(array_merge($row, ['question' => 'In Lagos Street Knowledge, which statement best describes Computer Village?']));
        $custom = $subject->questions()->create(array_merge($row, ['question' => 'My locally written question?']));
        $oldShortTemplate = $subject->questions()->create(array_merge($row, ['question' => 'Which statement best describes Computer Village?']));
        $attempt = ExamAttempt::create([
            'user_id' => User::factory()->create()->id, 'mode' => 'rush', 'requested_question_count' => 1,
            'question_count' => 1, 'duration_seconds' => 60, 'started_at' => now(), 'ends_at' => now()->addMinute(),
        ]);
        $snapshot = $attempt->questions()->create([
            'question_id' => $old->id, 'subject_id' => $subject->id, 'position' => 1,
            'question_text' => $old->question, 'correct_answer' => 'option_a',
            'option_a' => 'Ikeja', 'option_b' => 'Ikoyi', 'option_c' => 'Epe', 'option_d' => 'Yaba',
        ]);
        (new LagosStreetKnowledgeSeeder)->run();
        $count = $subject->questions()->count();
        (new LagosStreetKnowledgeSeeder)->run();
        $this->assertSame($count, $subject->questions()->count());
        $this->assertDatabaseMissing('questions', ['id' => $old->id]);
        $this->assertDatabaseMissing('questions', ['id' => $oldShortTemplate->id]);
        $this->assertDatabaseHas('questions', ['id' => $custom->id]);
        $this->assertSame($old->question, $snapshot->fresh()->question_text);
        $this->assertNull($snapshot->fresh()->question_id);
        $this->assertDatabaseHas('questions', ['subject_id' => $subject->id, 'question' => "Where's Computer Village?"]);
    }

    public function test_lagos_prompts_are_short_unique_and_have_four_distinct_options(): void
    {
        $rows = (new LagosQuestionBank)->rows();
        $this->assertCount(72, $rows);
        $this->assertCount(72, array_unique(array_column($rows, 'question')));
        foreach ($rows as $row) {
            $this->assertLessThanOrEqual(90, mb_strlen($row['question']));
            $this->assertStringNotContainsString('Lagos Street Knowledge', $row['question']);
            $this->assertCount(4, array_unique(array_intersect_key($row, array_flip(['option_a', 'option_b', 'option_c', 'option_d']))));
            $this->assertNotEmpty($row[$row['answer']]);
        }
    }
}
