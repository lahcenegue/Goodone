<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceEarningsTable extends Migration
{
    public function up()
    {
        Schema::create('service_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Service provider ID
            $table->unsignedBigInteger('order_id'); // Reference to the order
            $table->unsignedBigInteger('service_id'); // Reference to service (for tracking)
            $table->decimal('gross_amount', 10, 2); // Total service price
            $table->decimal('platform_fee_amount', 10, 2)->default(0); // Platform fees deducted
            $table->decimal('tax_amount', 10, 2)->default(0); // Tax amount deducted
            $table->decimal('coupon_discount', 10, 2)->default(0); // Coupon discount (platform absorbs this)
            $table->decimal('net_earnings', 10, 2); // Final amount service provider earns
            $table->string('pricing_type'); // hourly, daily, fixed
            $table->decimal('duration_value', 8, 2); // hours/days worked
            $table->string('region'); // Tax region
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('earned_at'); // When the service was completed
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('order');
            $table->foreign('service_id')->references('id')->on('services');
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_earnings');
    }
}