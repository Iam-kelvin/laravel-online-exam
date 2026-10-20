<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Services\PuzzleBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    public function index()
    {
        return view('games.index', ['games' => PuzzleBank::GAMES]);
    }

    public function start(Request $request, string $game)
    {
        abort_unless(isset(PuzzleBank::GAMES[$game]), 404);
        $duration = max(15, min(180, (int) round((int) $request->query('duration_seconds', 60) / 15) * 15));

        return view('games.start', ['game' => $game, 'details' => PuzzleBank::GAMES[$game], 'duration' => $duration]);
    }

    public function store(Request $request, string $game, PuzzleBank $bank)
    {
        abort_unless(isset(PuzzleBank::GAMES[$game]), 404);
        $validated = $request->validate([
            'duration_seconds' => ['required', 'integer', Rule::in(range(15, 180, 15))],
        ]);
        $questions = collect($bank->questions($game))->shuffle()->values();
        $attempt = DB::transaction(function () use ($validated, $game, $questions) {
            $attempt = ExamAttempt::create([
                'user_id' => auth()->id(), 'mode' => 'rush', 'game_type' => $game,
                'requested_question_count' => $questions->count(), 'question_count' => $questions->count(),
                'duration_seconds' => $validated['duration_seconds'],
                'started_at' => now(), 'ends_at' => now()->addSeconds($validated['duration_seconds']),
            ]);
            $questions->each(fn ($question, $index) => $attempt->questions()->create(
                array_merge($question, ['position' => $index + 1])
            ));
            $startedAt = now();
            $attempt->update(['started_at' => $startedAt, 'ends_at' => $startedAt->copy()->addSeconds($validated['duration_seconds'])]);

            return $attempt;
        });

        return redirect()->route('exam.take', $attempt);
    }
}
