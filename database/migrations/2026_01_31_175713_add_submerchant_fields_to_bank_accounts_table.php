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
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->enum('sub_merchant_type', ['PERSONAL', 'PRIVATE_COMPANY', 'LIMITED_OR_JOINT_STOCK_COMPANY'])->nullable()->after('branch_name');
            $table->string('tax_office')->nullable()->after('sub_merchant_type');
            $table->string('tax_number')->nullable()->after('tax_office');
            $table->string('legal_company_title')->nullable()->after('tax_number');
            $table->string('identity_number')->nullable()->after('legal_company_title');
            $table->string('contact_name')->nullable()->after('identity_number');
            $table->string('contact_surname')->nullable()->after('contact_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn([
                'sub_merchant_type',
                'tax_office',
                'tax_number',
                'legal_company_title',
                'identity_number',
                'contact_name',
                'contact_surname',
            ]);
        });
    }
};
