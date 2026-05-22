<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamPreset;
use Illuminate\Http\Request;

class ExamPresetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presets = ExamPreset::orderBy('question_count')->get();

        return view('admin.exam-presets.index', compact('presets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'question_count' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'active' => 'nullable|boolean',
        ]);

        ExamPreset::create([
            'label' => $validated['label'],
            'question_count' => $validated['question_count'],
            'duration_seconds' => $validated['duration_minutes'] * 60,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('exam-presets.index')->with('success', 'Exam duration preset created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamPreset $examPreset)
    {
        return view('admin.exam-presets.edit', compact('examPreset'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamPreset $examPreset)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
            'question_count' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
            'active' => 'nullable|boolean',
        ]);

        $examPreset->update([
            'label' => $validated['label'],
            'question_count' => $validated['question_count'],
            'duration_seconds' => $validated['duration_minutes'] * 60,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('exam-presets.index')->with('success', 'Exam duration preset updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamPreset $examPreset)
    {
        if ($examPreset->attempts()->exists()) {
            return redirect()
                ->route('exam-presets.index')
                ->with('error', 'Presets with exam attempts cannot be deleted. Deactivate it instead.');
        }

        $examPreset->delete();

        return redirect()->route('exam-presets.index')->with('success', 'Exam duration preset deleted successfully.');
    }
}
