<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'share_token',
        'user_id',
        'exam_preset_id',
        'requested_question_count',
        'question_count',
        'duration_seconds',
        'time_used_seconds',
        'started_at',
        'ends_at',
        'submitted_at',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ends_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ExamAttempt $attempt) {
            if (! $attempt->share_token) {
                $attempt->share_token = static::makeShareToken();
            }
        });
    }

    public static function makeShareToken(): string
    {
        do {
            $token = Str::upper(Str::random(7));
        } while (static::where('share_token', $token)->exists());

        return $token;
    }

    public function ensureShareToken(): string
    {
        if (! $this->share_token) {
            $this->forceFill(['share_token' => static::makeShareToken()])->save();
        }

        return $this->share_token;
    }

    public function scorePercent(): int
    {
        return $this->question_count > 0
            ? (int) round(($this->score / $this->question_count) * 100)
            : 0;
    }

    public function scoreForSubject(Subject $subject): int
    {
        return $this->questionsForSubject($subject)
            ->where('is_correct', true)
            ->count();
    }

    public function questionCountForSubject(Subject $subject): int
    {
        return $this->questionsForSubject($subject)->count();
    }

    public function scorePercentForSubject(Subject $subject): int
    {
        $count = $this->questionCountForSubject($subject);

        return $count > 0
            ? (int) round(($this->scoreForSubject($subject) / $count) * 100)
            : 0;
    }

    public function resolvedTimeUsedSeconds(): int
    {
        if ($this->time_used_seconds !== null) {
            return (int) $this->time_used_seconds;
        }

        if ($this->submitted_at && $this->started_at) {
            return max(1, min($this->duration_seconds, $this->started_at->diffInSeconds($this->submitted_at)));
        }

        return $this->duration_seconds;
    }

    public function timeUsedLabel(): string
    {
        $seconds = $this->resolvedTimeUsedSeconds();
        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        return $minutes > 0
            ? $minutes . 'm ' . str_pad((string) $remainingSeconds, 2, '0', STR_PAD_LEFT) . 's'
            : $remainingSeconds . 's';
    }

    public function examName(): string
    {
        $names = $this->subjects->pluck('name')->filter();

        if ($names->isEmpty()) {
            return 'CrazyExam Challenge';
        }

        return $names->count() === 1 ? $names->first() : $names->join(', ');
    }

    public function publicDisplayName(): string
    {
        return $this->user ? $this->user->publicName() : 'CrazyExam learner';
    }

    public function subjectPerformance(): Collection
    {
        $questions = $this->relationLoaded('questions')
            ? $this->questions
            : $this->questions()->with('subject')->get();

        return $questions
            ->groupBy('subject_id')
            ->map(function (Collection $questions) {
                $total = $questions->count();
                $correct = $questions->where('is_correct', true)->count();
                $subject = $questions->first()?->subject;

                return [
                    'name' => $subject?->name ?? 'CrazyExam',
                    'correct' => $correct,
                    'total' => $total,
                    'percent' => $total > 0 ? (int) round(($correct / $total) * 100) : 0,
                ];
            })
            ->values();
    }

    public function highlightedSubjectName(): string
    {
        $performance = $this->subjectPerformance();

        if ($performance->isEmpty()) {
            return $this->examName();
        }

        $bestPercent = $performance->max('percent');
        $bestCorrect = $performance
            ->where('percent', $bestPercent)
            ->max('correct');
        $candidates = $performance
            ->where('percent', $bestPercent)
            ->where('correct', $bestCorrect)
            ->values();

        if ($candidates->count() <= 1) {
            return $candidates->first()['name'];
        }

        $index = abs(crc32($this->share_token ?: (string) $this->id)) % $candidates->count();

        return $candidates->get($index)['name'];
    }

    public function reportHeadline(): string
    {
        $name = $this->publicDisplayName();
        $percent = $this->scorePercent();
        $subjects = $this->subjects->pluck('name')->filter()->values();

        if ($subjects->isEmpty()) {
            return "{$name} scored {$percent}% on CrazyExam";
        }

        if ($subjects->count() === 1) {
            return "{$name} knows {$percent}% about {$subjects->first()}";
        }

        if ($subjects->count() === 2) {
            return "{$name} knows {$percent}% about {$subjects->get(0)} + {$subjects->get(1)}";
        }

        return "{$name} is strongest in {$this->highlightedSubjectName()}";
    }

    public function reportSubtitle(): string
    {
        $subjects = $this->subjects->pluck('name')->filter()->values();

        if ($subjects->count() > 2) {
            return "Scored {$this->scorePercent()}% across {$subjects->count()} exam banks";
        }

        return "{$this->score} / {$this->question_count} correct in {$this->timeUsedLabel()}";
    }

    private function questionsForSubject(Subject $subject): Collection
    {
        $questions = $this->relationLoaded('questions')
            ? $this->questions
            : $this->questions()->get();

        return $questions->where('subject_id', $subject->id);
    }

    public function shortLink(): string
    {
        return url('/s/' . $this->ensureShareToken());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preset()
    {
        return $this->belongsTo(ExamPreset::class, 'exam_preset_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class)->withTimestamps();
    }

    public function questions()
    {
        return $this->hasMany(ExamAttemptQuestion::class)->orderBy('position');
    }
}
