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
        Schema::create('class_cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('cancelled_by_user_id')->nullable()->constrained('users')->onDelete('set null')->comment('İptal/erteleyen kullanıcı (admin veya antrenör)');
            $table->enum('cancellation_type', ['cancelled', 'postponed'])->default('cancelled')->comment('İptal mi erteleme mi');
            $table->date('original_date')->comment('Orijinal ders tarihi');
            $table->date('new_date')->nullable()->comment('Yeni tarih (eğer erteleme ise)');
            $table->text('reason')->nullable()->comment('İptal/erteleme nedeni');
            $table->enum('status', ['pending', 'scheduled', 'completed'])->default('pending')->comment('Durum: bekliyor, planlandı, tamamlandı');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_cancellations');
    }
};
