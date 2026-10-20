<?php

use Illuminate\Database\Migrations\Migration;
use Database\Seeders\LagosStreetKnowledgeSeeder;

return new class extends Migration
{
    public function up(): void
    {
        (new LagosStreetKnowledgeSeeder)->run();
    }

    public function down(): void
    {
        // Saved attempts keep their original question snapshots. Keep the revised bank.
    }
};
