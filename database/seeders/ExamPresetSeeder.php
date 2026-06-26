<?php

namespace Database\Seeders;

use App\Models\ExamPreset;
use Illuminate\Database\Seeder;

class ExamPresetSeeder extends Seeder
{
    /**
     * Seed realistic exam timing presets.
     */
    public function run(): void
    {
        $presets = [
            ['label' => '10 Questions', 'question_count' => 10, 'duration_seconds' => 10 * 60],
            ['label' => '20 Questions', 'question_count' => 20, 'duration_seconds' => 20 * 60],
            ['label' => '30 Questions', 'question_count' => 30, 'duration_seconds' => 35 * 60],
            ['label' => '50 Questions', 'question_count' => 50, 'duration_seconds' => 60 * 60],
            ['label' => '70 Questions', 'question_count' => 70, 'duration_seconds' => 90 * 60],
        ];

        foreach ($presets as $preset) {
            ExamPreset::query()->updateOrCreate(
                ['question_count' => $preset['question_count']],
                [
                    'label' => $preset['label'],
                    'duration_seconds' => $preset['duration_seconds'],
                    'active' => true,
                ]
            );
        }
    }
}
