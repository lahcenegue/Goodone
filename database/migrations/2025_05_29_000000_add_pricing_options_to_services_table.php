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
        Schema::table('services', function (Blueprint $table) {
            $table->decimal('cost_per_day', 8, 2)->nullable()->after('cost_per_hour');
            $table->decimal('fixed_price', 8, 2)->nullable()->after('cost_per_day');
            $table->enum('pricing_type', ['hourly', 'daily', 'fixed'])->default('hourly')->after('fixed_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['cost_per_day', 'fixed_price', 'pricing_type']);
        });
    }
};