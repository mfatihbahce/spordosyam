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
        Schema::table('schools', function (Blueprint $table) {
            $table->string('iyzico_api_key')->nullable()->after('demo_expires_at');
            $table->string('iyzico_secret_key')->nullable()->after('iyzico_api_key');
            $table->string('iyzico_sub_merchant_key')->nullable()->after('iyzico_secret_key');
            $table->decimal('iyzico_commission_rate', 5, 2)->default(5.00)->after('iyzico_sub_merchant_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'iyzico_api_key',
                'iyzico_secret_key',
                'iyzico_sub_merchant_key',
                'iyzico_commission_rate',
            ]);
        });
    }
};
