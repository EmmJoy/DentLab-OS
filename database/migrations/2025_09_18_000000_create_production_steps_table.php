<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('production_steps')) {
            Schema::create('production_steps', function (Blueprint $table) {
                $table->id();
                $table->string('predict3d_id');
                $table->unsignedInteger('step_number');
                $table->unsignedInteger('upper_value')->nullable();
                $table->unsignedInteger('lower_value')->nullable();
                $table->timestamps();

                $table->unique(['predict3d_id', 'step_number']);
                $table->index('predict3d_id');

                // If your DB supports it and patients table exists at migration time, keep FK; otherwise skip
                // $table->foreign('predict3d_id')->references('Predict3DId')->on('patients')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_steps');
    }
};
