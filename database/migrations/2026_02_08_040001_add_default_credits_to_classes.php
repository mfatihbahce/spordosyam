<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->unsignedInteger('default_credits')->default(8)->after('end_date')->nullable()->comment('Bu sınıfa kayıt olan öğrenci için varsayılan ders hakkı');
        });

        DB::table('classes')->update(['default_credits' => 8]);
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('default_credits');
        });
    }
};
