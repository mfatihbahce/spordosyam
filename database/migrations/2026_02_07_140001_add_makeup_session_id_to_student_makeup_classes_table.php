<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_makeup_classes', function (Blueprint $table) {
            $table->foreignId('makeup_session_id')->nullable()->after('scheduled_class_id')->constrained('makeup_sessions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_makeup_classes', function (Blueprint $table) {
            $table->dropForeign(['makeup_session_id']);
        });
    }
};
