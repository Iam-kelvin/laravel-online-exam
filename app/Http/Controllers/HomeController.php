<?php

namespace App\Http\Controllers;

use App\Models\SiteFact;
use App\Models\Subject;
use App\Services\LeaderboardService;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(LeaderboardService $leaderboard)
    {
        $user = auth()->user();
        $recentAttempts = $user->examAttempts()
            ->with('subjects')
            ->latest()
            ->take(5)
            ->get();

        $totals = $user->examAttempts()->whereNotNull('submitted_at')
            ->selectRaw('COUNT(*) AS completed, COALESCE(SUM(score), 0) AS score_total, COALESCE(SUM(question_count), 0) AS question_total')
            ->first();
        $inProgressAttempt = $user->examAttempts()
            ->whereNull('submitted_at')
            ->unexpired()
            ->latest()
            ->first();
        $bestAttempt = $user->examAttempts()
            ->whereNotNull('submitted_at')
            ->orderByDesc('score')
            ->latest()
            ->first();

        $facts = SiteFact::where('active', true)->inRandomOrder()->take(8)->get();

        return view('home', [
            'stats' => [
                'completed_attempts' => (int) $totals->completed,
                'average_score' => $totals->question_total > 0 ? round(($totals->score_total / $totals->question_total) * 100) : null,
                'best_score' => $bestAttempt ? "{$bestAttempt->score} / {$bestAttempt->question_count}" : null,
                'in_progress_attempt_id' => optional($inProgressAttempt)->id,
            ],
            'bankGroups' => Subject::withCount('questions')
                ->where('active', true)
                ->whereNotNull('bank_type')
                ->orderBy('bank_type')
                ->orderBy('name')
                ->get()
                ->groupBy('bank_type'),
            'recentAttempts' => $recentAttempts,
            'weeklyLeaders' => $leaderboard->weekly(),
            'allTimeLeaders' => $leaderboard->allTime(),
            'facts' => $facts,
            'factItems' => $facts->map(fn ($fact) => [
                'kind' => ucfirst($fact->kind),
                'title' => $fact->title,
                'body' => $fact->body,
            ])->values(),
        ]);
    }
}
