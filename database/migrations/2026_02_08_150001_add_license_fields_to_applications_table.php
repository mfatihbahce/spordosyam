<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('license_type', 20)->nullable()->after('demo_expires_at')->comment('demo, free, paid');
            $table->decimal('paid_amount', 12, 2)->nullable()->after('license_type');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['license_type', 'paid_amount']);
        });
    }
};
