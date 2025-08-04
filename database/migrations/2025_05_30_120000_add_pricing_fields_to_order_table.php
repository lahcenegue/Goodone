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
        Schema::table('order', function (Blueprint $table) {
            // Add new columns for pricing system
            $table->enum('pricing_type', ['hourly', 'daily', 'fixed'])->default('hourly')->after('price');
            $table->decimal('duration_value', 8, 2)->nullable()->after('pricing_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn(['pricing_type', 'duration_value']);
        });
    }
};