<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('production_steps') && Schema::hasColumn('production_steps', 'patient_predict3d_id')) {
            // Make the legacy column nullable so inserts without it succeed
            DB::statement('ALTER TABLE `production_steps` MODIFY `patient_predict3d_id` VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        // No-op safe rollback
    }
};
