<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('bank_type')->default('academic')->after('slug');
            $table->string('description')->nullable()->after('bank_type');
        });

        DB::table('subjects')
            ->whereNull('bank_type')
            ->orWhere('bank_type', '')
            ->update(['bank_type' => 'academic']);
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['bank_type', 'description']);
        });
    }
};
