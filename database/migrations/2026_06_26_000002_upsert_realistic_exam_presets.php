<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        $presets = [
            ['label' => '10 Questions', 'question_count' => 10, 'duration_seconds' => 10 * 60],
            ['label' => '20 Questions', 'question_count' => 20, 'duration_seconds' => 20 * 60],
            ['label' => '30 Questions', 'question_count' => 30, 'duration_seconds' => 35 * 60],
            ['label' => '50 Questions', 'question_count' => 50, 'duration_seconds' => 60 * 60],
            ['label' => '70 Questions', 'question_count' => 70, 'duration_seconds' => 90 * 60],
        ];

        foreach ($presets as $preset) {
            DB::table('exam_presets')->updateOrInsert(
                ['question_count' => $preset['question_count']],
                [
                    'label' => $preset['label'],
                    'duration_seconds' => $preset['duration_seconds'],
                    'active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('exam_presets')
            ->whereIn('question_count', [10, 20, 70])
            ->delete();

        DB::table('exam_presets')
            ->where('question_count', 30)
            ->update([
                'label' => '30 Questions',
                'duration_seconds' => 20 * 60,
                'updated_at' => now(),
            ]);

        DB::table('exam_presets')
            ->where('question_count', 50)
            ->update([
                'label' => '50 Questions',
                'duration_seconds' => 60 * 60,
                'updated_at' => now(),
            ]);
    }
};
