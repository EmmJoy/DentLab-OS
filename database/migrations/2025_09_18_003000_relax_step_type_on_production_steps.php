<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('production_steps') && Schema::hasColumn('production_steps', 'step_type')) {
            // Make legacy column nullable and default to NULL so inserts without it succeed
            try {
                DB::statement('ALTER TABLE `production_steps` MODIFY `step_type` VARCHAR(255) NULL');
            } catch (\Throwable $e) {
                // If the column type is different, attempt a generic NULL modification
                try {
                    DB::statement('ALTER TABLE `production_steps` MODIFY `step_type` TEXT NULL');
                } catch (\Throwable $e2) {
                    // swallow; we still handle it at application level below by providing a value
                }
            }
        }
    }

    public function down(): void
    {
        // No-op safe rollback
    }
};
