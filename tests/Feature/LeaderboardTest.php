<?php

namespace Tests\Feature;

use App\Models\ExamAttempt;
use App\Models\User;
use App\Services\LeaderboardService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_database_ranking_keeps_percentage_score_time_and_submission_tiebreaks(): void
    {
        $user = User::factory()->create();
        $fixtures = collect([
            [9, 10, 20, 0], [90, 100, 30, 0], [90, 100, 10, 0],
            [90, 100, 10, 1], [100, 100, 100, 0], [1, 1, 1, 0],
            [0, 0, 2, 0], [4, 7, null, 0], [8, 14, null, 0],
        ])->map(fn ($row) => ExamAttempt::create([
            'user_id' => $user->id, 'score' => $row[0], 'question_count' => $row[1],
            'requested_question_count' => $row[1], 'duration_seconds' => 120,
            'time_used_seconds' => $row[2], 'started_at' => now()->subMinutes(2),
            'ends_at' => now(), 'submitted_at' => now()->subDays($row[3]),
        ]));
        $unsubmitted = ExamAttempt::create([
            'user_id' => $user->id, 'score' => 100, 'question_count' => 100,
            'requested_question_count' => 100, 'duration_seconds' => 60,
            'started_at' => now(), 'ends_at' => now()->addMinute(),
        ]);
        $service = app(LeaderboardService::class);
        foreach ([null, now()->startOfWeek()] as $from) {
            $expected = ExamAttempt::whereNotNull('submitted_at')
                ->when($from, fn ($query) => $query->where('submitted_at', '>=', $from))
                ->get()->sort(fn ($a, $b) => [
                    -$a->scorePercent(), -$a->score, $a->resolvedTimeUsedSeconds(), $a->submitted_at->getTimestamp(), $a->id,
                ] <=> [
                    -$b->scorePercent(), -$b->score, $b->resolvedTimeUsedSeconds(), $b->submitted_at->getTimestamp(), $b->id,
                ])->values();
            $leaders = $from ? $service->weekly() : $service->allTime();
            $this->assertSame($expected->take(5)->pluck('id')->all(), $leaders->pluck('id')->all());
            foreach ($leaders as $leader) {
                $this->assertFalse($leader->relationLoaded('questions'));
                $this->assertTrue($leader->relationLoaded('user'));
                $this->assertTrue($leader->relationLoaded('subjects'));
            }
            foreach ($fixtures as $attempt) {
                $index = $expected->search(fn ($ranked) => $ranked->id === $attempt->id);
                $this->assertSame($index === false ? null : $index + 1, $service->rankFor($attempt, $from));
            }
        }
        $this->assertNull($service->rankFor($unsubmitted));
    }
}
