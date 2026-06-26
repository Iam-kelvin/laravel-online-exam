<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class LeaderboardService
{
    public function weekly(int $limit = 5): Collection
    {
        return $this->topAttempts(now()->startOfWeek(), null, $limit);
    }

    public function allTime(int $limit = 5): Collection
    {
        return $this->topAttempts(null, null, $limit);
    }

    public function topForSubject(Subject $subject, int $limit = 5): Collection
    {
        return $this->topAttempts(null, $subject, $limit);
    }

    public function rankFor(ExamAttempt $attempt, ?Carbon $from = null): ?int
    {
        if (! $attempt->submitted_at) {
            return null;
        }

        $ranked = $this->rankedAttempts($from);
        $index = $ranked->search(fn (ExamAttempt $rankedAttempt) => $rankedAttempt->id === $attempt->id);

        return $index === false ? null : $index + 1;
    }

    private function topAttempts(?Carbon $from = null, ?Subject $subject = null, int $limit = 5): Collection
    {
        return $this->rankedAttempts($from, $subject)->take($limit)->values();
    }

    private function rankedAttempts(?Carbon $from = null, ?Subject $subject = null): Collection
    {
        return ExamAttempt::query()
            ->with(['user', 'subjects', 'questions'])
            ->whereNotNull('submitted_at')
            ->when($from, fn ($query) => $query->where('submitted_at', '>=', $from))
            ->when($subject, fn ($query) => $query->whereHas('questions', fn ($questionQuery) => $questionQuery->where('subject_id', $subject->id)))
            ->get()
            ->sort(function (ExamAttempt $first, ExamAttempt $second) use ($subject) {
                if ($subject) {
                    return [
                        $second->scorePercentForSubject($subject),
                        $second->scoreForSubject($subject),
                        -$second->resolvedTimeUsedSeconds(),
                        -$second->submitted_at->getTimestamp(),
                    ] <=> [
                        $first->scorePercentForSubject($subject),
                        $first->scoreForSubject($subject),
                        -$first->resolvedTimeUsedSeconds(),
                        -$first->submitted_at->getTimestamp(),
                    ];
                }

                return [
                    $second->scorePercent(),
                    $second->score,
                    -$second->resolvedTimeUsedSeconds(),
                    -$second->submitted_at->getTimestamp(),
                ] <=> [
                    $first->scorePercent(),
                    $first->score,
                    -$first->resolvedTimeUsedSeconds(),
                    -$first->submitted_at->getTimestamp(),
                ];
            })
            ->values();
    }
}
