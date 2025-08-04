<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = "order";
    protected $guarded = ['id'];

    // Explicitly define fillable fields for clarity
    protected $fillable = [
        'duration_value',
        'start_at',
        'location',
        'region',
        'service_id',
        'price',
        'total_hours',
        'pricing_type',
        'status',
        'note',
        'user_id',
        'taxed_amount',
        'customer_platform_fee',
        'platform_fee_amount',
        'coupon_id',
        'coupon_percentage',
        'discounted_amount'
    ];

    public function Service()
    {
        return $this->belongsTo('App\Models\Service', 'service_id');
    }

    public function User()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Get the earnings record for this order
     */
    public function earnings()
    {
        return $this->hasOne(ServiceEarning::class);
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
