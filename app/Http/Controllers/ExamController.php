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
    public function lagosRush()
    {
        $subject = Subject::query()
            ->where('active', true)
            ->whereHas('questions')
            ->where(function ($query) {
                $query->where('slug', 'lagos-street-knowledge')
                    ->orWhereRaw('LOWER(name) = ?', ['lagos street knowledge']);
            })
            ->first();

        if (! $subject) {
            return redirect()->route('exam.start', ['mode' => 'rush'])
                ->with('warning', 'Lagos Rush is unavailable right now. Try another Rush subject while we get it ready.');
        }

        return redirect()->route('exam.start', [
            'mode' => 'rush', 'subject_ids' => [$subject->id], 'duration_seconds' => 60,
        ]);
    }

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

        $requestedCount = $validated['mode'] === 'rush' ? PHP_INT_MAX : (int) $validated['question_count'];
        $duration = (int) $validated['duration_seconds'];
        $selectedQuestions = $this->selectQuestions($subjects, $requestedCount);
        if ($validated['mode'] === 'rush') {
            $requestedCount = $selectedQuestions->count();
        }

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

            $attempt->addQuestionSnapshots($selectedQuestions->map(function (Question $question, int $index) {
                return [
                    'question_id' => $question->id,
                    'subject_id' => $question->subject_id,
                    'position' => $index + 1,
                    'question_text' => $question->question,
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                    'correct_answer' => $question->answer,
                ];
            }));

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

        if ($attempt->submitted_at && $attempt->isGame()) {
            return redirect()->route('games.start', ['game' => $attempt->game_type, 'duration_seconds' => $attempt->duration_seconds])
                ->with('status', 'That game has finished. Start a fresh round when you are ready.');
        }
        if ($attempt->submitted_at) {
            return redirect()->route('exam.review', $attempt);
        }

        $attempt->load(['subjects', 'questions.subject']);
        $endsAt = $attempt->ends_at->toIso8601String();

        if ($attempt->isRush()) {
            if ($attempt->remainingSeconds() <= 0) {
                return $this->submit(request(), $attempt);
            }

            $wrongSounds = $sounds->wrongAnswers();
            $introSound = $sounds->intro();

            return response()->view('exam.rush', compact('attempt', 'endsAt', 'wrongSounds', 'introSound'))
                ->header('Cache-Control', 'private, no-store, max-age=0');
        }

        return response()->view('exam.take', compact('attempt', 'endsAt'))
            ->header('Cache-Control', 'private, no-store, max-age=0');
    }

    public function answer(Request $request, int $attempt, int $question)
    {
        $receivedAt = now();
        $processingStarted = hrtime(true);
        return DB::transaction(function () use ($request, $attempt, $question, $receivedAt, $processingStarted) {
            $attempt = ExamAttempt::whereKey($attempt)->lockForUpdate()->firstOrFail();
            abort_unless($attempt->user_id === auth()->id(), 403);
            abort_unless($attempt->isRush(), 404);

            if ($attempt->submitted_at || $attempt->remainingSeconds($receivedAt) <= 0) {
                if (! $attempt->submitted_at) {
                    $this->finishAttempt($attempt);
                }

                return response()->json([
                    'message' => 'This rush has finished.',
                    'review_url' => route('exam.review', $attempt),
                ], 410);
            }

            // Fetch the prompt and progress together, without separate count/next queries.
            $progress = $attempt->questions()->reorder()->selectRaw(
                'COUNT(selected_answer) AS answered, COALESCE(SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END), 0) AS correct_count, MIN(CASE WHEN selected_answer IS NULL THEN position END) AS next_position'
            );
            $question = ExamAttemptQuestion::query()
                ->crossJoinSub($progress, 'progress')
                ->select('exam_attempt_questions.*', 'progress.answered', 'progress.correct_count', 'progress.next_position')
                ->where('exam_attempt_questions.id', $question)
                ->where('exam_attempt_questions.exam_attempt_id', $attempt->id)
                ->firstOrFail();
            $validated = $request->validate($question->isTyped() ? [
                'skip' => ['sometimes', 'boolean'],
                'answer' => ['required_unless:skip,true', 'nullable', 'string', 'max:120'],
            ] : [
                'answer' => ['required', Rule::in(['option_a', 'option_b', 'option_c', 'option_d'])],
            ]);
            $answered = (int) $question->answered;
            $score = (int) $question->correct_count;

            // A retry returns the first result; an answer can never be changed after marking.
            if ($question->selected_answer === null) {
                abort_unless((int) $question->next_position === $question->position, 409, 'Answer the current question first.');

                $skipped = $question->isTyped() && ($validated['skip'] ?? false);
                $answer = $skipped ? '[Skipped]' : $validated['answer'];
                $question->update([
                    'selected_answer' => $answer,
                    'is_correct' => ! $skipped && $question->matchesAnswer($answer),
                ]);
                $answered++;
                $score += (int) $question->is_correct;
                // Credit only work measured on the server, once per saved answer.
                $attempt->processing_time_ms += (int) ceil((hrtime(true) - $processingStarted) / 1e6);
                $attempt->save();
            }

            $finished = $answered === $attempt->question_count;

            if ($finished) {
                $this->finishAttempt($attempt, $score);
            }

            return response()->json([
                'is_correct' => $question->is_correct,
                'selected_answer' => $question->selected_answer,
                'correct_answer' => $question->correct_answer,
                'answered' => $answered,
                'score' => $score,
                'finished' => $finished,
                'review_url' => route('exam.review', $attempt),
                'remaining_seconds' => $attempt->remainingSeconds(),
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
                foreach ($attempt->questions->chunk(200) as $chunk) {
                    $rows = $chunk->map(function (ExamAttemptQuestion $question) use ($answers) {
                        $selectedAnswer = $answers[$question->id] ?? null;
                        $question->fill([
                            'selected_answer' => $selectedAnswer,
                            'is_correct' => $selectedAnswer !== null && $selectedAnswer === $question->correct_answer,
                        ]);
                        $question->updateTimestamps();

                        return $question->getAttributes();
                    })->all();
                    ExamAttemptQuestion::upsert($rows, ['id'], ['selected_answer', 'is_correct', 'updated_at']);
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

    private function finishAttempt(ExamAttempt $attempt, ?int $score = null): void
    {
        $submittedAt = now();
        $attempt->questions()->whereNull('is_correct')->update(['is_correct' => false]);
        $attempt->update([
            'score' => $score ?? $attempt->questions()->where('is_correct', true)->count(),
            'time_used_seconds' => max(1, min($attempt->duration_seconds, (int) floor($attempt->started_at->floatDiffInSeconds($submittedAt) - ($attempt->processing_time_ms ?? 0) / 1000))),
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

        $banks = Question::query()->whereIn('subject_id', $subjectIds)->inRandomOrder()
            ->when($subjectCount === 1 && $requestedCount !== PHP_INT_MAX, fn ($query) => $query->limit($requestedCount))
            ->get()->groupBy('subject_id');
        foreach ($subjectIds as $subjectId) {
            $pools[$subjectId] = $banks->get($subjectId, collect())->all();
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
