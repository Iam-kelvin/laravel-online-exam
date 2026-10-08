<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptQuestion;
use App\Models\Question;
use App\Models\Subject;
use App\Services\RushSounds;

class ExamController extends Controller
{
    public function start(Request $request)
    {
        $subjects = Subject::query()
            ->where('active', true)
            ->withCount('questions')
            ->orderBy('bank_type')
            ->orderBy('name')
            ->get();
        $subjectGroups = $subjects->groupBy('bank_type');

        $selectedSubjectIds = collect($request->query('subject_ids', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values()
            ->all();
        $selectedMode = $request->query('mode') === 'rush' ? 'rush' : 'standard';
        $selectedQuestionCount = (int) $request->query('question_count', 10);
        $selectedDuration = (int) $request->query('duration_seconds', $selectedMode === 'rush' ? 60 : 300);
        if ($selectedMode === 'rush') {
            $selectedDuration = max(15, min(180, (int) round($selectedDuration / 15) * 15));
        }
        $comboSource = $request->query('combo');

        return view('exam.start', compact(
            'subjects',
            'subjectGroups',
            'selectedSubjectIds',
            'selectedMode',
            'selectedQuestionCount',
            'selectedDuration',
            'comboSource'
        ));
    }

    public function store(Request $request)
    {
        if ($request->has('duration_value')) {
            $durationInput = $request->validate([
                'duration_value' => ['required', 'numeric', 'min:0.01', 'max:86400'],
                'duration_unit' => ['required', Rule::in(['seconds', 'minutes'])],
            ]);
            $seconds = (float) $durationInput['duration_value'] * ($durationInput['duration_unit'] === 'minutes' ? 60 : 1);
            $request->merge(['duration_seconds' => (int) round($seconds)]);
        }

        $validated = $request->validate([
            'mode' => ['required', Rule::in(['standard', 'rush'])],
            'subject_ids' => ['required', 'array', 'min:1', $request->input('mode') === 'rush' ? 'max:1' : 'max:100'],
            'subject_ids.*' => ['integer', 'distinct', Rule::exists('subjects', 'id')->where('active', true)],
            'question_count' => ['exclude_if:mode,rush', 'required', 'integer', 'min:1', 'max:10000'],
            'duration_seconds' => $request->input('mode') === 'rush'
                ? ['required', 'integer', Rule::in(range(15, 180, 15))]
                : ['required', 'integer', 'min:1', 'max:86400'],
        ], [
            'subject_ids.required' => 'Choose at least one subject.',
            'subject_ids.min' => 'Choose at least one subject.',
            'subject_ids.max' => 'Rush exams use exactly one subject.',
            'subject_ids.*.exists' => 'Choose an active subject.',
            'question_count.required' => 'Choose how many questions to answer.',
            'duration_seconds.required' => 'Choose an exam duration.',
            'duration_seconds.in' => 'Choose a Rush duration from 15 to 180 seconds, in 15-second steps.',
        ], [
            'subject_ids' => 'subject',
            'question_count' => 'question count',
            'duration_seconds' => 'duration in seconds',
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

        $requestedCount = $validated['mode'] === 'rush'
            ? Question::where('subject_id', $subjects->first()->id)->count()
            : (int) $validated['question_count'];
        $duration = (int) $validated['duration_seconds'];
        $selectedQuestions = $this->selectQuestions($subjects, $requestedCount);

        if ($selectedQuestions->isEmpty()) {
            return back()->withInput()->with('error', 'No questions are available for the selected subjects yet.');
        }

        $now = now();

        $attempt = DB::transaction(function () use ($subjects, $validated, $requestedCount, $duration, $selectedQuestions, $now) {
            $attempt = ExamAttempt::create([
                'user_id' => auth()->id(),
                'mode' => $validated['mode'],
                'requested_question_count' => $requestedCount,
                'question_count' => $selectedQuestions->count(),
                'duration_seconds' => $duration,
                'started_at' => $now,
                'ends_at' => $now->copy()->addSeconds($duration),
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

            // Preparing a large question bank should not consume the learner's time.
            $startedAt = now();
            $attempt->update([
                'started_at' => $startedAt,
                'ends_at' => $startedAt->copy()->addSeconds($duration),
            ]);

            return $attempt;
        });

        $redirect = redirect()->route('exam.take', $attempt);

        if ($attempt->question_count < $attempt->requested_question_count) {
            return $redirect->with(
                'warning',
                "{$attempt->question_count} questions were available from the selected subjects, so this attempt was created with {$attempt->question_count} questions."
            );
        }

        return $redirect;
    }

    public function take(ExamAttempt $attempt, RushSounds $sounds)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if ($attempt->submitted_at) {
            return redirect()->route('exam.review', $attempt);
        }

        $attempt->load(['subjects', 'questions.subject']);
        $endsAt = $attempt->ends_at->toIso8601String();

        if ($attempt->isRush()) {
            if (now()->greaterThanOrEqualTo($attempt->ends_at)) {
                return $this->submit(request(), $attempt);
            }

            $wrongSounds = $sounds->wrongAnswers();
            $introSound = $sounds->intro();

            return view('exam.rush', compact('attempt', 'endsAt', 'wrongSounds', 'introSound'));
        }

        return view('exam.take', compact('attempt', 'endsAt'));
    }

    public function answer(Request $request, ExamAttempt $attempt, ExamAttemptQuestion $question)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);
        abort_unless($attempt->isRush() && $question->exam_attempt_id === $attempt->id, 404);

        $validated = $request->validate([
            'answer' => ['required', Rule::in(['option_a', 'option_b', 'option_c', 'option_d'])],
        ]);

        return DB::transaction(function () use ($attempt, $question, $validated) {
            $attempt = ExamAttempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();

            if ($attempt->submitted_at || now()->greaterThanOrEqualTo($attempt->ends_at)) {
                if (! $attempt->submitted_at) {
                    $this->finishAttempt($attempt);
                }

                return response()->json([
                    'message' => 'This rush has finished.',
                    'review_url' => route('exam.review', $attempt),
                ], 410);
            }

            $question->refresh();

            // A retry returns the first result; an answer can never be changed after marking.
            if ($question->selected_answer === null) {
                $nextId = $attempt->questions()->whereNull('selected_answer')->value('id');
                abort_unless($nextId === $question->id, 409, 'Answer the current question first.');

                $question->update([
                    'selected_answer' => $validated['answer'],
                    'is_correct' => $validated['answer'] === $question->correct_answer,
                ]);
            }

            $answered = $attempt->questions()->whereNotNull('selected_answer')->count();
            $score = $attempt->questions()->where('is_correct', true)->count();
            $finished = $answered === $attempt->question_count;

            if ($finished) {
                $this->finishAttempt($attempt);
            }

            return response()->json([
                'is_correct' => $question->is_correct,
                'selected_answer' => $question->selected_answer,
                'correct_answer' => $question->correct_answer,
                'answered' => $answered,
                'score' => $score,
                'finished' => $finished,
                'review_url' => route('exam.review', $attempt),
            ]);
        });
    }

    public function submit(Request $request, ExamAttempt $attempt)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if ($attempt->submitted_at) {
            return redirect()->route('exam.review', $attempt)->with('warning', 'This exam has already been submitted.');
        }

        $answers = $attempt->isRush() ? [] : ($request->validate([
            'answers' => ['sometimes', 'array'],
            'answers.*' => [Rule::in(['option_a', 'option_b', 'option_c', 'option_d'])],
        ])['answers'] ?? []);

        DB::transaction(function () use ($attempt, $answers) {
            $attempt = ExamAttempt::whereKey($attempt->id)->lockForUpdate()->firstOrFail();

            if ($attempt->submitted_at) {
                return;
            }

            if (! $attempt->isRush()) {
                foreach ($attempt->questions as $question) {
                    $selectedAnswer = $answers[$question->id] ?? null;
                    $question->update([
                        'selected_answer' => $selectedAnswer,
                        'is_correct' => $selectedAnswer !== null && $selectedAnswer === $question->correct_answer,
                    ]);
                }
            }

            $this->finishAttempt($attempt);
        });

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

    public function review(ExamAttempt $attempt, RushSounds $sounds)
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        if (! $attempt->submitted_at) {
            return redirect()->route('exam.take', $attempt)->with('warning', 'Submit this exam before reviewing answers.');
        }

        $attempt->load(['subjects', 'questions' => function ($query) use ($attempt) {
            $query->with('subject');
            if ($attempt->isRush()) {
                $query->whereNotNull('selected_answer');
            }
        }]);
        $resultSound = $attempt->isRush()
            ? $sounds->result($attempt->score, $attempt->questions->count())
            : null;

        return view('exam.review', compact('attempt', 'resultSound'));
    }

    private function finishAttempt(ExamAttempt $attempt): void
    {
        $submittedAt = now();
        $attempt->questions()->whereNull('is_correct')->update(['is_correct' => false]);
        $attempt->update([
            'score' => $attempt->questions()->where('is_correct', true)->count(),
            'time_used_seconds' => max(1, min($attempt->duration_seconds, $attempt->started_at->diffInSeconds($submittedAt))),
            'submitted_at' => $submittedAt,
        ]);
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
