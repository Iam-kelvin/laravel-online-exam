<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_facts', function (Blueprint $table) {
            $table->id();
            $table->string('kind')->default('fact');
            $table->string('title')->nullable();
            $table->text('body');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_facts');
    }
};
