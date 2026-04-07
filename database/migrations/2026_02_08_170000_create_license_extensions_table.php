<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->date('extended_at');
            $table->unsignedInteger('days_added');
            $table->decimal('amount', 12, 2)->nullable()->comment('Superadmin bu uzatım için aldığı ücret (₺)');
            $table->foreignId('extended_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_extensions');
    }
};
