<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceEarning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id', 
        'service_id',
        'gross_amount',
        'platform_fee_amount',
        'tax_amount',
        'coupon_discount',
        'customer_platform_fee',      // NEW: Fee paid by customer
        'provider_platform_fee',      // NEW: Fee paid by provider  
        'platform_earnings_total',    // NEW: Total platform earnings
        'platform_fee_fixed',         // NEW: Fixed fee at time of order
        'platform_fee_percentage',    // NEW: Percentage at time of order
        'net_earnings',
        'pricing_type',
        'duration_value',
        'region',
        'status',
        'earned_at'
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'platform_fee_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'coupon_discount' => 'decimal:2',
        'customer_platform_fee' => 'decimal:2',      // NEW
        'provider_platform_fee' => 'decimal:2',      // NEW
        'platform_earnings_total' => 'decimal:2',    // NEW
        'platform_fee_fixed' => 'decimal:2',         // NEW
        'platform_fee_percentage' => 'decimal:2',    // NEW
        'net_earnings' => 'decimal:2',
        'duration_value' => 'decimal:2',
        'earned_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}