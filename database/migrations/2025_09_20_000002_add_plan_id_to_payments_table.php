<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments') && !Schema::hasColumn('payments', 'plan_id')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('id');
                $table->index('plan_id', 'idx_payments_plan');
                $table->foreign('plan_id')
                      ->references('id')
                      ->on('payment_plans')
                      ->onUpdate('cascade')
                      ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'plan_id')) {
            Schema::table('payments', function (Blueprint $table) {
                try { $table->dropForeign(['plan_id']); } catch (\Throwable $e) {}
                try { $table->dropIndex('idx_payments_plan'); } catch (\Throwable $e) {}
                $table->dropColumn('plan_id');
            });
        }
    }
};
