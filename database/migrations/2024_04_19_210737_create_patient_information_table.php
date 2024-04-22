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
        Schema::create('patient_information', function (Blueprint $table) {
            $table->id();
            $table->enum('gender',['male','female']);
            $table->date('dob');
            $table->integer('patient_id')->unsigned();
            $table->string('class');
            $table->string('address');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_information');
    }
};
