<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Subject;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subjects = Subject::query()
            ->where(fn ($query) => $query->where('active', true)->orHas('questions'))
            ->with(['questions' => fn ($query) => $query->latest()])
            ->withCount('questions')
            ->orderBy('name')
            ->get();

        $unassignedQuestions = Question::query()
            ->whereNull('subject_id')
            ->latest()
            ->get();

        $totalQuestions = $subjects->sum('questions_count') + $unassignedQuestions->count();

        return view('admin.questions.index', compact('subjects', 'unassignedQuestions', 'totalQuestions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.questions.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer' => 'required|in:option_a,option_b,option_c,option_d',
        ]);

        Question::create($request->only([
            'subject_id',
            'question',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'answer',
        ]));

        return redirect()->route('questions.index')
            ->with('success', 'Question created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.questions.edit', compact('question', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'question' => 'required',
            'option_a' => 'required',
            'option_b' => 'required',
            'option_c' => 'required',
            'option_d' => 'required',
            'answer' => 'required|in:option_a,option_b,option_c,option_d',
        ]);

        $question->update($request->only([
            'subject_id',
            'question',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'answer',
        ]));

        return redirect()->route('questions.index')
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
