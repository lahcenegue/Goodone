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