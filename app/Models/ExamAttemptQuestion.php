<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttemptQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_attempt_id',
        'question_id',
        'subject_id',
        'position',
        'question_text',
        'question_type',
        'prompt_data',
        'answer_aliases',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_answer',
        'selected_answer',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'prompt_data' => 'array',
        'answer_aliases' => 'array',
    ];

    public function isTyped(): bool
    {
        return in_array($this->question_type, ['scramble', 'rebus'], true);
    }

    public function matchesAnswer(string $answer): bool
    {
        if (! $this->isTyped()) {
            return $answer === $this->correct_answer;
        }
        $normalise = function (string $value): string {
            $value = \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($value));
            if ($this->question_type === 'rebus') {
                $value = preg_replace('/\b(?:a|an|the)\b/', '', $value);
            }

            return preg_replace('/[^a-z0-9]/', '', $value);
        };

        return in_array($normalise($answer), array_map($normalise, array_merge(
            [$this->correct_answer], $this->answer_aliases ?? []
        )), true);
    }

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'exam_attempt_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
