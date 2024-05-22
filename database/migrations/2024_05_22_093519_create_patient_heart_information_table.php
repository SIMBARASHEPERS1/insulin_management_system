<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_heart_information', function (Blueprint $table) {
            $table->id();
            $table->string('heart_rate');
            $table->string('blood_pressure')->default('0.00');
            $table->string('blood_oxygen')->default('0.00');
            $table->string('temperature')->default('36.0');
            $table->string('patient_id');
            $table->string('mhr');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_heart_information');
    }
};
