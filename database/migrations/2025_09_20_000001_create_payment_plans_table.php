<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payment_plans')) {
            Schema::create('payment_plans', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('Predict3DId');
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('payment_method', 50)->default('cash');
                $table->boolean('is_installment')->default(false);
                $table->boolean('locked')->default(false);
                $table->timestamps();

                $table->index('Predict3DId', 'idx_payment_plans_predict3d');
                // Foreign key to patients table (Predict3DId is PK in your app)
                $table->foreign('Predict3DId')
                      ->references('Predict3DId')
                      ->on('patients')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payment_plans')) {
            Schema::table('payment_plans', function (Blueprint $table) {
                try { $table->dropForeign(['Predict3DId']); } catch (\Throwable $e) {}
            });
            Schema::dropIfExists('payment_plans');
        }
    }
};
