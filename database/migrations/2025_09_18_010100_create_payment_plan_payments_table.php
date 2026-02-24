<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('payment_plan_payments')) {
            Schema::create('payment_plan_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payment_plan_id');
                $table->decimal('amount', 10, 2);
                $table->date('payment_date');
                $table->enum('payment_method', ['cash','card','bank_transfer'])->default('cash');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->index('payment_plan_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_plan_payments');
    }
};
