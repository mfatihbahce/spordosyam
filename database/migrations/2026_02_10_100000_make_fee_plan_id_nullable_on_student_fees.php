<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropForeign(['fee_plan_id']);
        });
        Schema::table('student_fees', function (Blueprint $table) {
            $table->unsignedBigInteger('fee_plan_id')->nullable()->change();
        });
        Schema::table('student_fees', function (Blueprint $table) {
            $table->foreign('fee_plan_id')->references('id')->on('fee_plans')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropForeign(['fee_plan_id']);
        });
        Schema::table('student_fees', function (Blueprint $table) {
            $table->unsignedBigInteger('fee_plan_id')->nullable(false)->change();
        });
        Schema::table('student_fees', function (Blueprint $table) {
            $table->foreign('fee_plan_id')->references('id')->on('fee_plans')->onDelete('cascade');
        });
    }
};
