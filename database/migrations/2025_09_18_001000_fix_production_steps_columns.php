<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('production_steps')) {
            return; // initial create migration will handle it
        }
        Schema::table('production_steps', function (Blueprint $table) {
            if (!Schema::hasColumn('production_steps', 'predict3d_id')) {
                $table->string('predict3d_id')->index();
            }
            if (!Schema::hasColumn('production_steps', 'step_number')) {
                $table->unsignedInteger('step_number');
            }
            if (!Schema::hasColumn('production_steps', 'upper_value')) {
                $table->unsignedInteger('upper_value')->nullable();
            }
            if (!Schema::hasColumn('production_steps', 'lower_value')) {
                $table->unsignedInteger('lower_value')->nullable();
            }
        });

        // Ensure unique index exists on (predict3d_id, step_number)
        // We will try to create it if it does not exist
        try {
            Schema::table('production_steps', function (Blueprint $table) {
                $table->unique(['predict3d_id', 'step_number'], 'ps_predict3d_step_unique');
            });
        } catch (\Throwable $e) {
            // ignore if already exists
        }
    }

    public function down(): void
    {
        // no-op safe down
    }
};
