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
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('subject_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->nullOnDelete();
        });

        $generalSubjectId = DB::table('subjects')->where('slug', 'general')->value('id');

        if (! $generalSubjectId) {
            $generalSubjectId = DB::table('subjects')->insertGetId([
                'name' => 'General',
                'slug' => 'general',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('questions')->whereNull('subject_id')->update([
            'subject_id' => $generalSubjectId,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subject_id');
        });
    }
};
