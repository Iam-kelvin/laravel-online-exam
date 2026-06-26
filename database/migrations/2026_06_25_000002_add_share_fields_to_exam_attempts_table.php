<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->string('share_token', 16)->nullable()->unique()->after('id');
            $table->unsignedInteger('time_used_seconds')->nullable()->after('duration_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn(['share_token', 'time_used_seconds']);
        });
    }
};
