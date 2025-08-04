<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceEarning;
use App\Models\AppSetting;
use App\Models\RegionTax;
use Illuminate\Support\Facades\Log;

class PopulateExistingEarningsData extends Migration
{
    public function up()
    {
        // Process all completed orders (status = 2) that don't have earnings records
        $completedOrders = Order::with('service')
            ->where('status', 2)
            ->whereDoesntHave('earnings')
            ->get();

        foreach ($completedOrders as $order) {
            if (!$order->service) {
                Log::warning('Skipping order with missing service', ['order_id' => $order->id]);
                continue;
            }

            try {
                // Calculate gross amount
                $gross_amount = 0;
                $pricing_type = $order->pricing_type ?? 'hourly'; // Default to hourly for old orders
                $duration_value = $order->total_hours ?? 1;
                
                // For old orders, try to determine pricing from service
                if ($order->service) {
                    if ($order->service->cost_per_hour && $pricing_type === 'hourly') {
                        $gross_amount = $order->service->cost_per_hour * $duration_value;
                    } elseif ($order->service->cost_per_day && $pricing_type === 'daily') {
                        $gross_amount = $order->service->cost_per_day * $duration_value;
                        $duration_value = $duration_value / 8; // Convert hours to days
                    } elseif ($order->service->fixed_price && $pricing_type === 'fixed') {
                        $gross_amount = $order->service->fixed_price;
                        $duration_value = 1;
                    } else {
                        // Fallback: use hourly rate
                        $gross_amount = ($order->service->cost_per_hour ?? 0) * $duration_value;
                    }
                }

                // If we still can't calculate, skip this order
                if ($gross_amount <= 0) {
                    Log::warning('Could not calculate gross amount for order', ['order_id' => $order->id]);
                    continue;
                }

                // Get platform fees settings
                $platform_percentage = 0;
                $_platform_percentage = AppSetting::where('key', 'platform_fees_percentage')->first();
                if ($_platform_percentage) {
                    $platform_percentage = $_platform_percentage->value;
                }
                
                $platform_fee_fixed = 0;
                $_platform_fee = AppSetting::where('key', 'platform_fees')->first();
                if ($_platform_fee) {
                    $platform_fee_fixed = $_platform_fee->value;
                }

                // Calculate platform fees
                $platform_fee_amount = ($gross_amount * $platform_percentage / 100) + $platform_fee_fixed;

                // Get tax information
                $region = $order->region ?? 'international';
                $tax = RegionTax::whereRaw('LOWER(region) = ?', [strtolower($region)])->first();
                if (!$tax) {
                    $tax = RegionTax::whereRaw('LOWER(region) = ?', ['international'])->first();
                }
                
                $tax_amount = 0;
                if ($tax) {
                    $tax_amount = $gross_amount * ($tax->percentage / 100);
                }

                // Coupon discount
                $coupon_discount = $order->discounted_amount ?? 0;

                // Calculate net earnings
                $net_earnings = $gross_amount - $platform_fee_amount - $tax_amount;

                // Create earnings record
                ServiceEarning::create([
                    'user_id' => $order->service->user_id,
                    'order_id' => $order->id,
                    'service_id' => $order->service_id,
                    'gross_amount' => $gross_amount,
                    'platform_fee_amount' => $platform_fee_amount,
                    'tax_amount' => $tax_amount,
                    'coupon_discount' => $coupon_discount,
                    'net_earnings' => $net_earnings,
                    'pricing_type' => $pricing_type,
                    'duration_value' => $duration_value,
                    'region' => $region,
                    'status' => 'completed',
                    'earned_at' => $order->updated_at ?? $order->created_at,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                Log::info('Created earnings record for existing order', [
                    'order_id' => $order->id,
                    'net_earnings' => $net_earnings
                ]);

            } catch (\Exception $e) {
                Log::error('Error creating earnings for existing order', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function down()
    {
        // Remove all earnings records created by this migration
        // We can identify them as they were created for orders with status = 2
        ServiceEarning::whereHas('order', function($query) {
            $query->where('status', 2);
        })->delete();
    }
}