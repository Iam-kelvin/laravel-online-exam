<?php

namespace App\Services;

use App\Models\ExamAttempt;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        if (! $attempt->submitted_at || ($from && $attempt->submitted_at->lt($from))) {
            return null;
        }

        $percent = $attempt->scorePercent();
        $time = $attempt->resolvedTimeUsedSeconds();
        return 1 + DB::query()->fromSub($this->overallQuery($from)->reorder(), 'ranked')
            ->where(function ($query) use ($attempt, $percent, $time) {
                $query->where('rank_percent', '>', $percent)
                    ->orWhere(fn ($q) => $q->where('rank_percent', $percent)->where('score', '>', $attempt->score))
                    ->orWhere(fn ($q) => $q->where('rank_percent', $percent)->where('score', $attempt->score)->where('rank_time', '<', $time))
                    ->orWhere(fn ($q) => $q->where('rank_percent', $percent)->where('score', $attempt->score)->where('rank_time', $time)->where('submitted_at', '<', $attempt->submitted_at))
                    ->orWhere(fn ($q) => $q->where('rank_percent', $percent)->where('score', $attempt->score)->where('rank_time', $time)->where('submitted_at', $attempt->submitted_at)->where('id', '<', $attempt->id));
            })->count();
    }

    private function topAttempts(?Carbon $from = null, ?Subject $subject = null, int $limit = 5): Collection
    {
        if (! $subject) {
            return $this->overallQuery($from)->with(['user', 'subjects'])->limit(max(0, $limit))->get();
        }
        return $this->rankedAttempts($from, $subject)->take($limit)->values();
    }

    private function overallQuery(?Carbon $from)
    {
        $elapsed = match (DB::connection()->getDriverName()) {
            'sqlite' => "ABS(CAST(strftime('%s', submitted_at) AS INTEGER) - CAST(strftime('%s', started_at) AS INTEGER))",
            'pgsql' => 'ABS(EXTRACT(EPOCH FROM (submitted_at - started_at)))',
            'sqlsrv' => 'ABS(DATEDIFF(second, started_at, submitted_at))',
            default => 'ABS(TIMESTAMPDIFF(SECOND, started_at, submitted_at))',
        };
        $boundedTime = "CASE WHEN {$elapsed} < 1 THEN 1 WHEN {$elapsed} > duration_seconds THEN duration_seconds ELSE {$elapsed} END";

        return ExamAttempt::query()->whereNotNull('submitted_at')
            ->when($from, fn ($query) => $query->where('submitted_at', '>=', $from))
            ->select('exam_attempts.*')
            ->selectRaw('ROUND(CASE WHEN question_count > 0 THEN 100.0 * score / question_count ELSE 0 END) AS rank_percent')
            ->selectRaw("COALESCE(time_used_seconds, CASE WHEN started_at IS NULL THEN duration_seconds ELSE {$boundedTime} END) AS rank_time")
            ->orderByDesc('rank_percent')->orderByDesc('score')->orderBy('rank_time')->orderBy('submitted_at')->orderBy('id');
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
