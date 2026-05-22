<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamPreset;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $completedAttempts = ExamAttempt::whereNotNull('submitted_at');
        $totalCompletedQuestions = (clone $completedAttempts)->sum('question_count');
        $totalScore = (clone $completedAttempts)->sum('score');

        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'subjects' => Subject::count(),
                'active_subjects' => Subject::where('active', true)->count(),
                'questions' => Question::count(),
                'active_presets' => ExamPreset::where('active', true)->count(),
                'attempts' => ExamAttempt::count(),
                'completed_attempts' => (clone $completedAttempts)->count(),
                'average_score' => $totalCompletedQuestions > 0 ? round(($totalScore / $totalCompletedQuestions) * 100) : null,
            ],
            'subjects' => Subject::withCount('questions')->orderByDesc('questions_count')->take(8)->get(),
            'presets' => ExamPreset::orderBy('question_count')->get(),
            'recentAttempts' => ExamAttempt::with(['user', 'subjects'])->latest()->take(8)->get(),
        ]);
    }
}
