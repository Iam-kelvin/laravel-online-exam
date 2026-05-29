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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'school_level')) {
                $table->string('school_level')->nullable();
            }

            if (! Schema::hasColumn('users', 'class_year')) {
                $table->string('class_year')->nullable();
            }

            if (! Schema::hasColumn('users', 'country_of_study')) {
                $table->string('country_of_study')->nullable();
            }

            if (! Schema::hasColumn('users', 'city_town')) {
                $table->string('city_town')->nullable();
            }
        });

        DB::table('users')->whereNull('school_level')->update(['school_level' => DB::raw('level')]);
        DB::table('users')->whereNull('class_year')->update(['class_year' => DB::raw('grade')]);
        DB::table('users')->whereNull('country_of_study')->update(['country_of_study' => DB::raw('country')]);
        DB::table('users')->whereNull('city_town')->update(['city_town' => DB::raw('county')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['school_level', 'class_year', 'country_of_study', 'city_town'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
