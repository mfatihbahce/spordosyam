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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('sport_branch_id');
            $table->unsignedBigInteger('coach_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('capacity')->default(20);
            $table->time('class_time')->nullable();
            $table->json('class_days')->nullable(); // ['monday', 'wednesday', 'friday']
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
