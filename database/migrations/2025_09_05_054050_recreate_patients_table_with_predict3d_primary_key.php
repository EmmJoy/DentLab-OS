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
        // First, drop foreign key constraints that reference patients
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
        });
        
        // Drop the existing patients table
        Schema::dropIfExists('patients');
        
        // Recreate the patients table with Predict3DId as primary key
        Schema::create('patients', function (Blueprint $table) {
            $table->string('Predict3DId')->primary();
            $table->enum('ScanningFor', ['Aligner', 'Zirconia', 'Others']);
            $table->string('ScanningForOthers')->nullable();
            $table->string('DoctorName');
            $table->string('ChamberName');
            $table->string('TerritoryName');
            $table->string('RegionalName');
            $table->string('PhoneNumber', 13);
            $table->string('EmergencyContact', 13);
            $table->enum('Gender', ['Male', 'Female', 'Custom']);
            $table->date('DateOfBirth');
            $table->text('Address');
            $table->integer('UpperCases')->nullable();
            $table->integer('LowerCases')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
        
        // Update the payments table to use string for patient_id
        Schema::table('payments', function (Blueprint $table) {
            $table->string('patient_id')->change();
        });
        
        // Recreate the foreign key constraint with the new primary key
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('patient_id')->references('Predict3DId')->on('patients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the patients table
        Schema::dropIfExists('patients');
        
        // Recreate the original patients table structure
        Schema::create('patients', function (Blueprint $table) {
            $table->id()->startingValue(1000);
            $table->string('FullName');
            $table->enum('ScanningFor', ['Aligner', 'Zirconia', 'Others']);
            $table->string('ScanningForOthers')->nullable();
            $table->string('DoctorName');
            $table->string('ChamberName');
            $table->string('TerritoryName');
            $table->string('RegionalName');
            $table->string('PhoneNumber', 13);
            $table->string('EmergencyContact', 13);
            $table->enum('Gender', ['Male', 'Female', 'Custom']);
            $table->date('DateOfBirth');
            $table->text('Address');
            $table->foreignId('created_by')->constrained('users');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }
};
