<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_class_history', function (Blueprint $table) {
            $table->unsignedInteger('total_credits')->default(8)->after('left_at')->comment('Bu kayıt için tanımlı ders hakkı');
            $table->unsignedInteger('used_credits')->default(0)->after('total_credits')->comment('Bu ders için kullanılan hak');
            $table->string('leave_reason', 32)->nullable()->after('used_credits')->comment('graduated=mezun, transferred=ayrıldı');
        });

        DB::table('student_class_history')->update([
            'total_credits' => 8,
            'used_credits' => 0,
        ]);
    }

    public function down(): void
    {
        Schema::table('student_class_history', function (Blueprint $table) {
            $table->dropColumn(['total_credits', 'used_credits', 'leave_reason']);
        });
    }
};
