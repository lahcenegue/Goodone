<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function Subcategory()
    {
        return $this->belongsTo('App\Models\Subcategory', 'subcategory_id');
    }

    public function Rating()
    {
        return $this->hasMany('App\Models\Rating', 'id');
    }

    /**
     * The attributes that are mass assignable.
     * FIXED: Changed from $guarded to $fillable for better security
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'phone',
        'type',
        'full_name',
        'picture',
        'email_verified_at',
        // Admin-specific fields (safe for APIs - they don't use mass assignment)
        'verified',
        'blocked',
        'active',
        'city',
        'country',
        'location',
        // Keep existing fields that might be used
        'subcategory_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'reset_token',
        'reset_token_expiry',
        'device_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'verified' => 'boolean',
        'blocked' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Add relationship for admin activity logs (admin panel only)
     */
    public function adminActivityLogs()
    {
        return $this->hasMany(AdminActivityLog::class, 'customer_id');
    }

    /**
     * Add relationship for customer transactions (admin panel only)
     */
    public function customerTransactions()
    {
        return $this->hasMany(CustomerTransaction::class, 'customer_id');
    }

    /**
     * Add relationship for customer sessions (admin panel only)
     */
    public function customerSessions()
    {
        return $this->hasMany(CustomerSession::class, 'customer_id');
    }

    /**
     * Boot method to handle cascading deletes
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // This will be called when user is being deleted
            // Additional cleanup can be done here if needed
        });
    }
}
