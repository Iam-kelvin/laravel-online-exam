<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_preset_id',
        'requested_question_count',
        'question_count',
        'duration_seconds',
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
