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
                $table->id();
                $table->string('predict3d_id');
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->enum('payment_method', ['cash','card','bank_transfer'])->default('cash');
                $table->boolean('is_installment')->default(false);
                $table->date('next_payment_date')->nullable();
                $table->decimal('remaining_amount', 10, 2)->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->unique('predict3d_id');
                $table->index('predict3d_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plans');
    }
};
