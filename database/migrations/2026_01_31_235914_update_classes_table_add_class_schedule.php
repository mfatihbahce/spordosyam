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
        Schema::table('classes', function (Blueprint $table) {
            // class_time alanını kaldır
            $table->dropColumn('class_time');
            // class_schedule JSON alanı ekle: {"monday": "14:00", "tuesday": "16:00", ...}
            $table->json('class_schedule')->nullable()->after('class_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            // Geri al: class_schedule kaldır, class_time ekle
            $table->dropColumn('class_schedule');
            $table->time('class_time')->nullable()->after('capacity');
        });
    }
};
