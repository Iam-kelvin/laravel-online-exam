<?php

namespace App\Http\Controllers;

use App\Models\ExamPreset;
use App\Models\Subject;

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
    public function index()
    {
        $user = auth()->user();
        $recentAttempts = $user->examAttempts()
            ->with('subjects')
            ->latest()
            ->take(5)
            ->get();

        $completedAttempts = $user->examAttempts()->whereNotNull('submitted_at')->count();
        $inProgressAttempt = $user->examAttempts()
            ->whereNull('submitted_at')
            ->where('ends_at', '>', now())
            ->latest()
            ->first();
        $totalScore = $user->examAttempts()->whereNotNull('submitted_at')->sum('score');
        $totalQuestions = $user->examAttempts()->whereNotNull('submitted_at')->sum('question_count');
        $bestAttempt = $user->examAttempts()
            ->whereNotNull('submitted_at')
            ->orderByDesc('score')
            ->latest()
            ->first();

        return view('home', [
            'stats' => [
                'completed_attempts' => $completedAttempts,
                'average_score' => $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100) : null,
                'best_score' => $bestAttempt ? "{$bestAttempt->score} / {$bestAttempt->question_count}" : null,
                'in_progress_attempt_id' => optional($inProgressAttempt)->id,
            ],
            'subjects' => Subject::withCount('questions')->where('active', true)->orderBy('name')->take(8)->get(),
            'presets' => ExamPreset::where('active', true)->orderBy('question_count')->get(),
            'recentAttempts' => $recentAttempts,
        ]);
    }
}
