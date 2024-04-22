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
        Schema::create('patient_athrometrics', function (Blueprint $table) {
            $table->id();
            $table->float('height');
            $table->float('weight');
            $table->float('bmi');
            $table->string('bmi_category');
            $table->integer('patient_id')->unsigned();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_athrometrics');
    }
};
