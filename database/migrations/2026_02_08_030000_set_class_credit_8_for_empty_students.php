<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ders hakkı null veya 0 olan öğrencilere 8 ders hakkı tanımla
        DB::table('students')
            ->where(function ($q) {
                $q->whereNull('class_credit')->orWhere('class_credit', 0);
            })
            ->update(['class_credit' => 8]);
    }

    public function down(): void
    {
        // Geri almak için bu öğrencileri tekrar null yapmayız (hangi kayıtlar güncellendi bilinmiyor)
    }
};
