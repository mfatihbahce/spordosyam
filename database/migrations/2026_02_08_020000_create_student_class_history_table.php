<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_class_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->dateTime('enrolled_at');
            $table->dateTime('left_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'left_at']);
        });

        $now = now();
        $rows = DB::table('students')->whereNotNull('class_id')->get(['id', 'class_id', 'created_at']);
        foreach ($rows as $row) {
            DB::table('student_class_history')->insert([
                'student_id' => $row->id,
                'class_id' => $row->class_id,
                'enrolled_at' => $row->created_at,
                'left_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_history');
    }
};
