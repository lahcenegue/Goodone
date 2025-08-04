<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSession extends Model
{
    use HasFactory;

    protected $table = 'customer_sessions'; // Explicitly set table name

    protected $fillable = [
        'customer_id',
        'session_token',
        'device_type',
        'device_name', 
        'ip_address',
        'location',
        'login_at',
        'logout_at',
        'is_active'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}