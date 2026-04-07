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
        Schema::create('student_makeup_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('makeup_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendance_id')->nullable()->constrained()->onDelete('set null')->comment('Yoklama kaydı (izinli öğrenci için)');
            $table->foreignId('scheduled_class_id')->nullable()->constrained('classes')->onDelete('set null')->comment('Telafi için seçilen ders');
            $table->date('scheduled_date')->nullable()->comment('Planlanan telafi tarihi');
            $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled'])->default('pending')->comment('Durum');
            $table->timestamps();
            
            // Bir öğrencinin aynı telafi dersi için tekrar kayıt olmasını engelle
            $table->unique(['student_id', 'makeup_class_id'], 'unique_student_makeup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_makeup_classes');
    }
};
