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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
