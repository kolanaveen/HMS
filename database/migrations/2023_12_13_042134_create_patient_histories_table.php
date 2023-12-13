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
        Schema::create('patient_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->string('title');
            $table->string('food_allergies')->nullable();
            $table->string('bleed_tendency')->nullable();
            $table->string('heart_disease')->nullable();
            $table->string('blood_pressure')->nullable();
            $table->string('diabetic')->nullable();
            $table->string('surgery')->nullable();
            $table->string('accident')->nullable();
            $table->string('family_medical_history')->nullable();
            $table->string('current_medication')->nullable();
            $table->string('female_pregnancy')->nullable();
            $table->string('breast_feeding')->nullable();
            $table->string('health_insurance')->nullable();
            $table->boolean('low_income')->nullable();
            $table->string('reference')->nullable();
            $table->string('others')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_histories');
    }
};
