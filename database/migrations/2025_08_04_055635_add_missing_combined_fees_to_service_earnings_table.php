<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingCombinedFeesToServiceEarningsTable extends Migration
{
    public function up()
    {
        Schema::table('service_earnings', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('service_earnings', 'customer_platform_fee')) {
                $table->decimal('customer_platform_fee', 10, 2)->default(0);
            }
            
            if (!Schema::hasColumn('service_earnings', 'provider_platform_fee')) {
                $table->decimal('provider_platform_fee', 10, 2)->default(0);
            }
            
            if (!Schema::hasColumn('service_earnings', 'platform_earnings_total')) {
                $table->decimal('platform_earnings_total', 10, 2)->default(0);
            }
            
            if (!Schema::hasColumn('service_earnings', 'platform_fee_fixed')) {
                $table->decimal('platform_fee_fixed', 8, 2)->default(0);
            }
            
            if (!Schema::hasColumn('service_earnings', 'platform_fee_percentage')) {
                $table->decimal('platform_fee_percentage', 5, 2)->default(0);
            }
        });
    }

    public function down()
    {
        Schema::table('service_earnings', function (Blueprint $table) {
            $columns_to_drop = [];
            
            if (Schema::hasColumn('service_earnings', 'customer_platform_fee')) {
                $columns_to_drop[] = 'customer_platform_fee';
            }
            if (Schema::hasColumn('service_earnings', 'provider_platform_fee')) {
                $columns_to_drop[] = 'provider_platform_fee';
            }
            if (Schema::hasColumn('service_earnings', 'platform_earnings_total')) {
                $columns_to_drop[] = 'platform_earnings_total';
            }
            if (Schema::hasColumn('service_earnings', 'platform_fee_fixed')) {
                $columns_to_drop[] = 'platform_fee_fixed';
            }
            if (Schema::hasColumn('service_earnings', 'platform_fee_percentage')) {
                $columns_to_drop[] = 'platform_fee_percentage';
            }
            
            if (!empty($columns_to_drop)) {
                $table->dropColumn($columns_to_drop);
            }
        });
    }
}