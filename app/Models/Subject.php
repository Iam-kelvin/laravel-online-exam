<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public const TYPE_ACADEMIC = 'academic';
    public const TYPE_CHALLENGE = 'challenge';

    public const TYPES = [
        self::TYPE_ACADEMIC => 'Academic Exam',
        self::TYPE_CHALLENGE => 'Challenge Exam',
    ];

    protected $fillable = ['name', 'slug', 'bank_type', 'description', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->bank_type] ?? 'Exam Bank';
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function examAttempts()
    {
        return $this->belongsToMany(ExamAttempt::class)->withTimestamps();
    }
}
