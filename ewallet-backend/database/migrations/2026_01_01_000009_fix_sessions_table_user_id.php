<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sessions')) {
            try {
                DB::statement('ALTER TABLE sessions ALTER COLUMN user_id TYPE VARCHAR(255);');
            } catch (\Throwable $e) {
                // Fallback for drivers that don't support raw ALTER
            }
        }
    }

    public function down(): void
    {
    }
};
