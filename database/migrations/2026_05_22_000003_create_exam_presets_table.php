<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_presets', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->unsignedInteger('question_count');
            $table->unsignedInteger('duration_seconds');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        DB::table('exam_presets')->insert([
            [
                'label' => '30 Questions',
                'question_count' => 30,
                'duration_seconds' => 20 * 60,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'label' => '50 Questions',
                'question_count' => 50,
                'duration_seconds' => 60 * 60,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_presets');
    }
};
