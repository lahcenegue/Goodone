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
        Schema::table('service_earnings', function (Blueprint $table) {
            // Add platform earnings tracking
            $table->decimal('customer_platform_fee', 10, 2)->default(0)->after('coupon_discount');
            $table->decimal('provider_platform_fee', 10, 2)->default(0)->after('customer_platform_fee');
            $table->decimal('platform_earnings_total', 10, 2)->default(0)->after('provider_platform_fee');
            
            // Add settings snapshot (to preserve fee structure at time of order)
            $table->decimal('platform_fee_fixed', 10, 2)->default(0)->after('platform_earnings_total');
            $table->decimal('platform_fee_percentage', 5, 2)->default(0)->after('platform_fee_fixed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_earnings', function (Blueprint $table) {
            $table->dropColumn([
                'customer_platform_fee',
                'provider_platform_fee', 
                'platform_earnings_total',
                'platform_fee_fixed',
                'platform_fee_percentage'
            ]);
        });
    }
};