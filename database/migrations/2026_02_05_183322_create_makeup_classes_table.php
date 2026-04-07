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
        Schema::create('makeup_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('cancellation_id')->nullable()->constrained('class_cancellations')->onDelete('cascade')->comment('Ders iptal/erteleme kaydı (eğer ders iptal/ertelendiyse)');
            $table->foreignId('original_class_id')->nullable()->constrained('classes')->onDelete('cascade')->comment('Orijinal ders (iptal/ertelenen)');
            $table->foreignId('scheduled_class_id')->nullable()->constrained('classes')->onDelete('set null')->comment('Telafi için planlanan ders');
            $table->date('scheduled_date')->nullable()->comment('Planlanan telafi tarihi');
            $table->enum('type', ['cancellation', 'excused'])->default('cancellation')->comment('Telafi tipi: ders iptal/erteleme veya izinli öğrenci');
            $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled'])->default('pending')->comment('Durum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makeup_classes');
    }
};
