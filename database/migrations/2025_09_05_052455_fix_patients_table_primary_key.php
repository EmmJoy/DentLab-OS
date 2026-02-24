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
        Schema::table('patients', function (Blueprint $table) {
            // Add new fields for Total Cases
            $table->integer('UpperCases')->nullable()->after('Address');
            $table->integer('LowerCases')->nullable()->after('UpperCases');
            
            // Add a new field for 3D Predict ID
            $table->string('Predict3DId')->nullable()->after('id');
            
            // Add a field for manual patient ID
            $table->string('ManualId')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Remove the added fields
            $table->dropColumn(['UpperCases', 'LowerCases', 'Predict3DId', 'ManualId']);
        });
    }
};
