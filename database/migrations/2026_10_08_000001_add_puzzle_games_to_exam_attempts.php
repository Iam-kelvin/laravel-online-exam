<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->string('game_type')->nullable();
        });
        Schema::table('exam_attempt_questions', function (Blueprint $table) {
            $table->string('question_type')->default('choice');
            $table->json('prompt_data')->nullable();
            $table->json('answer_aliases')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempt_questions', function (Blueprint $table) {
            $table->dropColumn(['question_type', 'prompt_data', 'answer_aliases']);
        });
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn('game_type');
        });
    }
};
