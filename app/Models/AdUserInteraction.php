<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AdUserInteraction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'ad_user_interactions';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ad_id',
        'user_id',
        'interaction_type',
        'placement',
        'device_type',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer_url',
        'country',
        'region',
        'city',
        'interaction_metadata',
        'cost_amount',
        'interaction_timestamp'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'interaction_metadata' => 'array',
        'cost_amount' => 'decimal:4',
        'interaction_timestamp' => 'datetime'
    ];

    /**
     * Interaction types
     */
    const TYPE_VIEW = 'view';
    const TYPE_CLICK = 'click';
    const TYPE_IMPRESSION = 'impression';
    const TYPE_CONVERSION = 'conversion';

    /**
     * Get available interaction types
     */
    public static function getInteractionTypes(): array
    {
        return [
            self::TYPE_VIEW => 'View',
            self::TYPE_CLICK => 'Click',
            self::TYPE_IMPRESSION => 'Impression',
            self::TYPE_CONVERSION => 'Conversion'
        ];
    }

    /**
     * Create new interaction record
     */
    public static function recordInteraction(
        int $adId,
        string $interactionType,
        string $placement,
        ?int $userId = null,
        array $metadata = []
    ): self {
        $settings = AdGlobalSetting::getCached();

        // Calculate cost based on interaction type
        $costAmount = 0;
        if ($interactionType === self::TYPE_VIEW) {
            $costAmount = $settings->cost_per_view;
        } elseif ($interactionType === self::TYPE_CLICK) {
            $costAmount = $settings->cost_per_click;
        }

        return self::create([
            'ad_id' => $adId,
            'user_id' => $userId,
            'interaction_type' => $interactionType,
            'placement' => $placement,
            'device_type' => self::detectDeviceType(),
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referrer_url' => request()->header('referer'),
            'country' => self::getLocationFromRequest()['country'] ?? null,
            'region' => self::getLocationFromRequest()['region'] ?? null,
            'city' => self::getLocationFromRequest()['city'] ?? null,
            'interaction_metadata' => $metadata,
            'cost_amount' => $costAmount,
            'interaction_timestamp' => now()
        ]);
    }

    /**
     * Detect device type from user agent
     */
    private static function detectDeviceType(): ?string
    {
        $userAgent = request()->userAgent() ?? '';

        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        } else {
            return 'web';
        }
    }

    /**
     * Get location data from request (simplified)
     */
    private static function getLocationFromRequest(): array
    {
        // In production, you would use a GeoIP service
        // For now, return basic data
        return [
            'country' => 'Canada',
            'region' => null,
            'city' => null
        ];
    }

    /**
     * Scope for specific ad
     */
    public function scopeForAd($query, int $adId)
    {
        return $query->where('ad_id', $adId);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for specific interaction type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('interaction_type', $type);
    }

    /**
     * Scope for date range
     */
    public function scopeInDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('interaction_timestamp', [$startDate, $endDate]);
    }

    /**
     * Relationships
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
