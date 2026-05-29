<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\ExamPreset;
use App\Models\Question;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        $countryExpression = "COALESCE(NULLIF(country_of_study, ''), NULLIF(country, ''), 'Unknown')";
        $cityExpression = "COALESCE(NULLIF(city_town, ''), NULLIF(county, ''), 'Unknown')";
        $levelExpression = "COALESCE(NULLIF(school_level, ''), NULLIF(level, ''), 'Unknown')";
        $classExpression = "COALESCE(NULLIF(class_year, ''), NULLIF(grade, ''), 'Unknown')";

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
            'countryInsights' => $this->userInsight($countryExpression),
            'cityInsights' => $this->userInsight($cityExpression),
            'levelInsights' => $this->userInsight($levelExpression),
            'classInsights' => $this->userInsight($classExpression),
        ]);
    }

    private function userInsight(string $expression)
    {
        $labels = User::query()->selectRaw($expression . ' as label');

        return DB::query()
            ->fromSub($labels, 'profile_labels')
            ->select('label')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('label')
            ->orderByDesc('total')
            ->orderBy('label')
            ->take(8)
            ->get();
    }
}
