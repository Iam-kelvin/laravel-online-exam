<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\ExamAttempt;
use App\Models\ExamPreset;
use App\Models\Question;
use App\Models\Subject;
use App\Services\LeaderboardService;

class ExamController extends Controller
{
    public function start(Request $request, LeaderboardService $leaderboard)
    {
        $subjects = Subject::query()
            ->where('active', true)
            ->withCount('questions')
            ->orderBy('bank_type')
            ->orderBy('name')
            ->get();
        $subjectGroups = $subjects->groupBy('bank_type');
        $bankLeaders = $subjects->mapWithKeys(function (Subject $subject) use ($leaderboard) {
            return [$subject->id => $leaderboard->topForSubject($subject, 1)->first()];
        });

        $presets = ExamPreset::query()
            ->where('active', true)
            ->orderBy('question_count')
            ->get();

        $selectedSubjectIds = collect($request->query('subject_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
        $selectedPresetId = (int) $request->query('exam_preset_id', 0);
        $comboSource = $request->query('combo');

        return view('exam.start', compact(
            'subjects',
            'subjectGroups',
            'bankLeaders',
            'presets',
            'selectedSubjectIds',
            'selectedPresetId',
            'comboSource'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_ids' => ['required', 'array', 'min:1'],
            'subject_ids.*' => ['integer', 'exists:subjects,id'],
            'exam_preset_id' => ['required', 'integer', 'exists:exam_presets,id'],
        ], [
            'subject_ids.required' => 'Choose at least one subject.',
            'subject_ids.min' => 'Choose at least one subject.',
            'exam_preset_id.required' => 'Choose a question count and time.',
            'exam_preset_id.exists' => 'Choose an available question count and time.',
        ], [
            'subject_ids' => 'subject',
            'exam_preset_id' => 'question count and time',
        ]);

        $selectedSubjectIds = collect($validated['subject_ids'])->map(fn ($id) => (int) $id)->unique()->values();
        $subjectsById = Subject::query()
            ->whereIn('id', $selectedSubjectIds)
            ->where('active', true)
            ->get()
            ->keyBy('id');

        $subjects = $selectedSubjectIds
            ->map(fn ($id) => $subjectsById->get($id))
            ->filter()
            ->values();

        if ($subjects->isEmpty()) {
            return back()->withInput()->with('error', 'Choose at least one active subject.');
        }

        $preset = ExamPreset::query()
            ->where('active', true)
            ->findOrFail($validated['exam_preset_id']);

        $selectedQuestions = $this->selectQuestions($subjects, $preset->question_count);

        if ($selectedQuestions->isEmpty()) {
            return back()->withInput()->with('error', 'No questions are available for the selected subjects yet.');
        }

        $now = now();

        $attempt = DB::transaction(function () use ($subjects, $preset, $selectedQuestions, $now) {
            $attempt = ExamAttempt::create([
                'user_id' => auth()->id(),
                'exam_preset_id' => $preset->id,
                'requested_question_count' => $preset->question_count,
                'question_count' => $selectedQuestions->count(),
                'duration_seconds' => $preset->duration_seconds,
                'started_at' => $now,
                'ends_at' => $now->copy()->addSeconds($preset->duration_seconds),
            ]);

            $attempt->subjects()->sync($subjects->pluck('id')->all());

            $selectedQuestions->each(function (Question $question, int $index) use ($attempt) {
                $attempt->questions()->create([
                    'question_id' => $question->id,
                    'subject_id' => $question->subject_id,
                    'position' => $index + 1,
                    'question_text' => $question->question,
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                    'correct_answer' => $question->answer,
                ]);
            });

            return $attempt;
        });

        $redirect = redirect()->route('exam.take', $attempt);

        if ($attempt->question_count < $attempt->requested_question_count && auth()->user()->can('manage-questions')) {
            return $redirect->with(
                'warning',
                "{$attempt->question_count} questions were available from the selected subjects, so this attempt was created with {$attempt->question_count} questions."
            );
        }

        return $redirect;
    }

    public function take(ExamAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if ($attempt->submitted_at) {
            return redirect()->route('exam.review', $attempt);
        }

        $attempt->load(['subjects', 'questions.subject']);
        $endsAt = $attempt->ends_at->toIso8601String();

        return view('exam.take', compact('attempt', 'endsAt'));
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if ($attempt->submitted_at) {
            return redirect()->route('exam.review', $attempt)->with('warning', 'This exam has already been submitted.');
        }

        $answers = $request->input('answers', []);
        $score = 0;

        $attempt->load('questions');
        $submittedAt = now();

        foreach ($attempt->questions as $question) {
            $selectedAnswer = $answers[$question->id] ?? null;
            $isCorrect = $selectedAnswer !== null && $selectedAnswer === $question->correct_answer;

            $question->update([
                'selected_answer' => $selectedAnswer,
                'is_correct' => $isCorrect,
            ]);

            if ($isCorrect) {
                $score++;
            }
        }

        $attempt->update([
            'score' => $score,
            'time_used_seconds' => max(1, min($attempt->duration_seconds, $attempt->started_at->diffInSeconds($submittedAt))),
            'submitted_at' => $submittedAt,
        ]);

        return redirect()->route('exam.review', $attempt)->with('success', 'Exam submitted successfully.');
    }

    public function results()
    {
        $examAttempts = auth()->user()
            ->examAttempts()
            ->with('subjects')
            ->latest()
            ->get();

        return view('exam.results', compact('examAttempts'));
    }

    public function review(ExamAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if (! $attempt->submitted_at) {
            return redirect()->route('exam.take', $attempt)->with('warning', 'Submit this exam before reviewing answers.');
        }

        $attempt->load(['subjects', 'questions.subject']);

        return view('exam.review', compact('attempt'));
    }

    private function selectQuestions(Collection $subjects, int $requestedCount): Collection
    {
        $subjectIds = $subjects->pluck('id')->all();
        $subjectCount = count($subjectIds);
        $baseTarget = intdiv($requestedCount, $subjectCount);
        $remainder = $requestedCount % $subjectCount;
        $selected = [];
        $pools = [];

        foreach ($subjectIds as $subjectId) {
            $pools[$subjectId] = Question::query()
                ->where('subject_id', $subjectId)
                ->inRandomOrder()
                ->get()
                ->all();
        }

        foreach ($subjectIds as $index => $subjectId) {
            $target = $baseTarget + ($index < $remainder ? 1 : 0);
            $available = count($pools[$subjectId]);
            $take = min($target, $available);

            foreach (array_splice($pools[$subjectId], 0, $take) as $question) {
                $selected[] = $question;
            }
        }

        while (count($selected) < $requestedCount) {
            $added = false;

            foreach ($subjectIds as $subjectId) {
                if (count($selected) >= $requestedCount) {
                    break;
                }

                if (empty($pools[$subjectId])) {
                    continue;
                }

                $selected[] = array_shift($pools[$subjectId]);
                $added = true;
            }

            if (! $added) {
                break;
            }
        }

        return collect($selected)->shuffle()->values();
    }
}
