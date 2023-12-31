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
            $table->text('food_allergies')->nullable();
            $table->text('bleed_tendency')->nullable();
            $table->text('heart_disease')->nullable();
            $table->text('blood_pressure')->nullable();
            $table->text('diabetic')->nullable();
            $table->text('surgery')->nullable();
            $table->text('accident')->nullable();
            $table->text('family_medical_history')->nullable();
            $table->text('current_medication')->nullable();
            $table->text('female_pregnancy')->nullable();
            $table->text('breast_feeding')->nullable();
            $table->text('health_insurance')->nullable();
            $table->boolean('low_income')->nullable();
            $table->text('reference')->nullable();
            $table->text('others')->nullable();
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
