<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_coach_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->onDelete('cascade');
            $table->foreignId('coach_id')->constrained('coaches')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            $table->unique(['parent_id', 'coach_id', 'student_id']);
        });

        Schema::create('parent_coach_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('parent_coach_conversations')->onDelete('cascade');
            $table->string('sender_type', 10); // 'parent' | 'coach'
            $table->unsignedBigInteger('sender_id'); // parents.id or coaches.id
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_coach_messages');
        Schema::dropIfExists('parent_coach_conversations');
    }
};
