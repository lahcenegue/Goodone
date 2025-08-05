<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdGlobalSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'ad_global_settings';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'ads_enabled',
        'max_ads_per_user_session',
        'ad_frequency_cap_minutes',
        'default_target_regions',
        'default_target_user_types',
        'require_admin_approval',
        'default_min_ctr_threshold',
        'default_max_daily_budget',
        'auto_pause_poor_performers',
        'ads_per_placement_limit',
        'show_ads_to_new_users',
        'show_ads_to_inactive_users',
        'cost_per_view',
        'cost_per_click',
        'track_user_behavior',
        'last_updated_by_admin',
        'last_settings_update',
        'settings_notes'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'ads_enabled' => 'boolean',
        'max_ads_per_user_session' => 'integer',
        'ad_frequency_cap_minutes' => 'integer',
        'default_target_regions' => 'array',
        'default_target_user_types' => 'array',
        'require_admin_approval' => 'boolean',
        'default_min_ctr_threshold' => 'decimal:2',
        'default_max_daily_budget' => 'decimal:2',
        'auto_pause_poor_performers' => 'boolean',
        'ads_per_placement_limit' => 'integer',
        'show_ads_to_new_users' => 'boolean',
        'show_ads_to_inactive_users' => 'boolean',
        'cost_per_view' => 'decimal:4',
        'cost_per_click' => 'decimal:4',
        'track_user_behavior' => 'boolean',
        'last_settings_update' => 'datetime'
    ];

    /**
     * Cache key for global settings
     */
    const CACHE_KEY = 'ad_global_settings';
    const CACHE_TTL = 3600; // 1 hour

    /**
     * Get cached global settings
     */
    public static function getCached()
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return self::first() ?? self::createDefault();
        });
    }

    /**
     * Create default settings if none exist
     */
    public static function createDefault()
    {
        return self::create([
            'ads_enabled' => true,
            'max_ads_per_user_session' => 10,
            'ad_frequency_cap_minutes' => 60,
            'default_target_user_types' => ['both'],
            'require_admin_approval' => false,
            'default_min_ctr_threshold' => 1.0,
            'auto_pause_poor_performers' => true,
            'ads_per_placement_limit' => 3,
            'show_ads_to_new_users' => true,
            'show_ads_to_inactive_users' => false,
            'cost_per_view' => 0.01,
            'cost_per_click' => 0.10,
            'track_user_behavior' => true
        ]);
    }

    /**
     * Clear settings cache
     */
    public static function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Update settings and clear cache
     */
    public function updateSettings(array $data, $adminId = null)
    {
        if ($adminId) {
            $data['last_updated_by_admin'] = $adminId;
            $data['last_settings_update'] = now();
        }

        $this->update($data);
        self::clearCache();

        return $this;
    }

    /**
     * Check if ads are globally enabled
     */
    public static function adsEnabled(): bool
    {
        return self::getCached()->ads_enabled;
    }

    /**
     * Get default settings for new ads
     */
    public static function getDefaults(): array
    {
        $settings = self::getCached();

        return [
            'target_regions' => $settings->default_target_regions,
            'target_user_types' => $settings->default_target_user_types,
            'min_ctr_threshold' => $settings->default_min_ctr_threshold,
            'max_daily_budget' => $settings->default_max_daily_budget,
            'approval_status' => $settings->require_admin_approval ? 'pending' : 'approved'
        ];
    }

    /**
     * Relationship with admin who last updated
     */
    public function lastUpdatedByAdmin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'last_updated_by_admin');
    }
}
