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
            $table->foreign('sport_branch_id')->references('id')->on('sport_branches')->onDelete('cascade');
            $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['sport_branch_id']);
            $table->dropForeign(['coach_id']);
        });
    }
};
