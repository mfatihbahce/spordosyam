<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('makeup_session_id')->nullable()->after('class_id')->constrained('makeup_sessions')->nullOnDelete();
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['makeup_session_id']);
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable(false)->change();
        });
    }
};
