<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fee_plans', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'day_of_month', 'description']);
        });
    }

    public function down(): void
    {
        Schema::table('fee_plans', function (Blueprint $table) {
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly'])->default('monthly')->after('amount');
            $table->integer('day_of_month')->default(1)->after('frequency');
            $table->text('description')->nullable()->after('day_of_month');
        });
    }
};
