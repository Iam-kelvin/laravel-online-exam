<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class ExamController extends Controller
{
    public $questions;
    
    public function __construct()
    {
        $this->questions = Question::all();
    }

    public function start()
    {
        $questions = $this->questions; // $questions = Question::all();
        $duration = $questions->sum('duration');
        return view('exam.start', compact('questions', 'duration'));
    }

    public function submit(Request $request)
    {
        $user = auth()->user();
        // $questions = Question::all();
        $answers = $request->input('answers');
        $questions = $this->questions;

        $score = 0;
        foreach ($questions as $question) {
            if (isset($answers[$question->id]) && $question->answer === $answers[$question->id]) {
                $score++;
            }
            // if ($question->answer === $answers[$question->id]) {
            //     $score++;
            // }
        }

        $user->examResults()->create([
            'score' => $score,
            'duration' => $questions->sum('duration')
        ]);

        return redirect()->route('exam.results')->with('success', 'Exam submitted successfully.');
    }

    public function results()
    {
        $user = auth()->user();
        $examResults = $user->examResults()->orderByDesc('created_at')->get();
        return view('exam.results', compact('examResults'));
    }
}
