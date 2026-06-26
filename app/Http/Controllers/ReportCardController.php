<?php

namespace App\Http\Controllers;

use App\Models\ExamAttempt;
use App\Models\ExamPreset;
use App\Services\LeaderboardService;

class ReportCardController extends Controller
{
    public function show(string $token, LeaderboardService $leaderboard)
    {
        $attempt = ExamAttempt::query()
            ->with(['user', 'subjects', 'questions.subject'])
            ->where('share_token', $token)
            ->firstOrFail();

        abort_unless($attempt->submitted_at, 404);

        $attempt->ensureShareToken();
        $shareUrl = $attempt->shortLink();
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=156x156&data=' . urlencode($shareUrl);

        return view('reports.show', [
            'attempt' => $attempt,
            'overallRank' => $leaderboard->rankFor($attempt),
            'weeklyRank' => $leaderboard->rankFor($attempt, now()->startOfWeek()),
            'shareUrl' => $shareUrl,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    public function takeCombo(string $token)
    {
        $attempt = ExamAttempt::query()
            ->with(['user', 'subjects'])
            ->where('share_token', $token)
            ->firstOrFail();

        abort_unless($attempt->submitted_at, 404);

        $subjectIds = $attempt->subjects->pluck('id')->all();
        abort_if(empty($subjectIds), 404);

        $presetId = null;

        if ($attempt->exam_preset_id) {
            $presetId = ExamPreset::query()
                ->where('active', true)
                ->whereKey($attempt->exam_preset_id)
                ->value('id');
        }

        if (! $presetId) {
            $presetId = ExamPreset::query()
                ->where('active', true)
                ->orderByRaw('ABS(question_count - ?)', [$attempt->requested_question_count])
                ->value('id');
        }

        return redirect()
            ->route('exam.start', [
                'subject_ids' => $subjectIds,
                'exam_preset_id' => $presetId,
                'combo' => $attempt->share_token,
            ])
            ->with('status', $attempt->publicDisplayName() . "'s combo is loaded. Start when ready.");
    }
}
