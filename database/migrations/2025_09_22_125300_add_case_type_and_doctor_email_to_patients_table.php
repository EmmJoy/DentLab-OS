<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'case_type')) {
                $table->string('case_type')->after('ScanningFor')->nullable(false)->default('Full Case');
            }
            if (!Schema::hasColumn('patients', 'doctor_email')) {
                $table->string('doctor_email')->nullable()->after('DoctorName');
            }
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'case_type')) {
                $table->dropColumn('case_type');
            }
            if (Schema::hasColumn('patients', 'doctor_email')) {
                $table->dropColumn('doctor_email');
            }
        });
    }
};
