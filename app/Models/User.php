<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
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
        'verified',
        'blocked',
        'active',
        'city',
        'country',
        'location',
        'subcategory_id',
        'device_token',
        // Keep existing reset token fields for password reset
        'reset_token',
        'reset_token_expiry',
        // Add new verification token fields for email verification
        'verification_token',
        'verification_token_expiry',
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
        'device_token',
        // Hide new verification token fields from API responses
        'verification_token',
        'verification_token_expiry',
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
        // Cast existing reset token expiry
        'reset_token_expiry' => 'datetime',
        // Cast new verification token expiry
        'verification_token_expiry' => 'datetime',
    ];

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

    /**
     * Relationships
     */
    public function Subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function Rating()
    {
        return $this->hasMany(Rating::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function adminActivityLogs()
    {
        return $this->hasMany(AdminActivityLog::class, 'customer_id');
    }

    public function customerTransactions()
    {
        return $this->hasMany(CustomerTransaction::class, 'customer_id');
    }

    public function customerSessions()
    {
        return $this->hasMany(CustomerSession::class, 'customer_id');
    }

    /**
     * Check if verification token is valid and not expired
     *
     * @param string $token
     * @return bool
     */
    public function isValidVerificationToken($token)
    {
        return $this->verification_token === $token 
            && $this->verification_token_expiry 
            && $this->verification_token_expiry->isFuture();
    }

    /**
     * Check if reset token is valid and not expired
     *
     * @param string $token
     * @return bool
     */
    public function isValidResetToken($token)
    {
        return $this->reset_token === $token 
            && $this->reset_token_expiry 
            && $this->reset_token_expiry->isFuture();
    }

    /**
     * Clear verification token
     */
    public function clearVerificationToken()
    {
        $this->update([
            'verification_token' => null,
            'verification_token_expiry' => null,
        ]);
    }

    /**
     * Clear reset token
     */
    public function clearResetToken()
    {
        $this->update([
            'reset_token' => null,
            'reset_token_expiry' => null,
        ]);
    }

    /**
     * Boot method - handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // Cascading delete logic here if needed
        });
    }
}