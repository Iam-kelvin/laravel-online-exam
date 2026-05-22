<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_preset_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('requested_question_count');
            $table->unsignedInteger('question_count');
            $table->unsignedInteger('duration_seconds');
            $table->dateTime('started_at');
            $table->dateTime('ends_at');
            $table->dateTime('submitted_at')->nullable();
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
