<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCombinedFeesToServiceEarningsTable extends Migration
{
    public function up()
    {
        Schema::table('service_earnings', function (Blueprint $table) {
            // Add columns for combined fee system
            $table->decimal('customer_platform_fee', 10, 2)->default(0)->after('coupon_discount');
            $table->decimal('provider_platform_fee', 10, 2)->default(0)->after('customer_platform_fee');
            $table->decimal('platform_earnings_total', 10, 2)->default(0)->after('provider_platform_fee');
            $table->decimal('platform_fee_fixed', 8, 2)->default(0)->after('platform_earnings_total');
            $table->decimal('platform_fee_percentage', 5, 2)->default(0)->after('platform_fee_fixed');
        });
    }

    public function down()
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
}