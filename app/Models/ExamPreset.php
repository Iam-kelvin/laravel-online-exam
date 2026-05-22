<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamPreset extends Model
{
    use HasFactory;

    protected $fillable = ['label', 'question_count', 'duration_seconds', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }
}
