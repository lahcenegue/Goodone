<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;

class Ad extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'ad_type',
        'placement',
        'target_url',
        'is_active',
        'display_order',
        'start_date',
        'end_date',
        'click_count',
        'view_count',

        // NEW: Admin Control Features
        'admin_priority',
        'auto_pause_enabled',
        'min_ctr_threshold',
        'max_daily_budget',
        'daily_spend',

        // NEW: Advanced Targeting
        'target_regions',
        'target_user_types',
        'target_activity_levels',
        'target_order_history',
        'exclude_regions',
        'exclude_user_types',

        // NEW: Admin Settings
        'admin_notes',
        'created_by_admin',
        'last_modified_by_admin',
        'approval_status',
        'performance_score',

        // NEW: Scheduling Templates
        'schedule_template',
        'recurring_schedule',
        'timezone'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'click_count' => 'integer',
        'view_count' => 'integer',
        'display_order' => 'integer',

        // NEW: Admin Control Casts
        'admin_priority' => 'integer',
        'auto_pause_enabled' => 'boolean',
        'min_ctr_threshold' => 'decimal:2',
        'max_daily_budget' => 'decimal:2',
        'daily_spend' => 'decimal:2',
        'performance_score' => 'decimal:2',

        // NEW: Targeting Arrays
        'target_regions' => 'array',
        'target_user_types' => 'array',
        'target_activity_levels' => 'array',
        'target_order_history' => 'array',
        'exclude_regions' => 'array',
        'exclude_user_types' => 'array',

        // NEW: Scheduling
        'recurring_schedule' => 'array'
    ];

    /**
     * Ad types enum values
     */
    const AD_TYPE_INTERNAL = 'internal';
    const AD_TYPE_EXTERNAL = 'external';

    /**
     * Placement enum values
     */
    const PLACEMENT_HOME_BANNER = 'home_banner';
    const PLACEMENT_SERVICE_LIST = 'service_list';
    const PLACEMENT_SERVICE_DETAIL = 'service_detail';
    const PLACEMENT_PROFILE = 'profile';

    /**
     * NEW: Admin Priority Levels
     */
    const PRIORITY_LOW = 1;
    const PRIORITY_NORMAL = 2;
    const PRIORITY_HIGH = 3;
    const PRIORITY_URGENT = 4;

    /**
     * NEW: Approval Status
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * NEW: User Activity Levels
     */
    const ACTIVITY_NEW = 'new';
    const ACTIVITY_LOW = 'low';
    const ACTIVITY_MEDIUM = 'medium';
    const ACTIVITY_HIGH = 'high';
    const ACTIVITY_VIP = 'vip';

    /**
     * Get all available ad types
     */
    public static function getAdTypes(): array
    {
        return [
            self::AD_TYPE_INTERNAL => 'Internal (App Features)',
            self::AD_TYPE_EXTERNAL => 'External (Third Party)'
        ];
    }

    /**
     * Get all available placements
     */
    public static function getPlacements(): array
    {
        return [
            self::PLACEMENT_HOME_BANNER => 'Home Page Banner',
            self::PLACEMENT_SERVICE_LIST => 'Service Listing Page',
            self::PLACEMENT_SERVICE_DETAIL => 'Service Details Page',
            self::PLACEMENT_PROFILE => 'Profile Page'
        ];
    }

    /**
     * NEW: Get admin priority levels
     */
    public static function getPriorityLevels(): array
    {
        return [
            self::PRIORITY_LOW => 'Low Priority',
            self::PRIORITY_NORMAL => 'Normal Priority',
            self::PRIORITY_HIGH => 'High Priority',
            self::PRIORITY_URGENT => 'Urgent Priority'
        ];
    }

    /**
     * NEW: Get approval statuses
     */
    public static function getApprovalStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending Approval',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected'
        ];
    }

    /**
     * NEW: Get activity levels
     */
    public static function getActivityLevels(): array
    {
        return [
            self::ACTIVITY_NEW => 'New Users (< 7 days)',
            self::ACTIVITY_LOW => 'Low Activity (1-2 orders)',
            self::ACTIVITY_MEDIUM => 'Medium Activity (3-10 orders)',
            self::ACTIVITY_HIGH => 'High Activity (11-50 orders)',
            self::ACTIVITY_VIP => 'VIP Users (50+ orders)'
        ];
    }

    /**
     * NEW: Get user types for targeting
     */
    public static function getUserTypes(): array
    {
        return [
            'customer' => 'Customers Only',
            'worker' => 'Service Providers Only',
            'both' => 'Both Customer & Providers'
        ];
    }

    /**
     * Scope for active ads
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for approved ads only
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', self::STATUS_APPROVED);
    }

    /**
     * Scope for ads that are currently scheduled to be shown
     */
    public function scopeScheduled($query)
    {
        $now = Carbon::now();
        return $query->where(function ($q) use ($now) {
            $q->where(function ($subQ) use ($now) {
                // No start date or start date has passed
                $subQ->whereNull('start_date')
                    ->orWhere('start_date', '<=', $now);
            })->where(function ($subQ) use ($now) {
                // No end date or end date hasn't passed
                $subQ->whereNull('end_date')
                    ->orWhere('end_date', '>=', $now);
            });
        });
    }

    /**
     * NEW: Scope for ads within budget
     */
    public function scopeWithinBudget($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_daily_budget')
                ->orWhereRaw('daily_spend < max_daily_budget');
        });
    }

    /**
     * NEW: Scope for performance-based filtering
     */
    public function scopeGoodPerformance($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('min_ctr_threshold')
                ->orWhereRaw('(click_count / GREATEST(view_count, 1)) * 100 >= min_ctr_threshold');
        });
    }

    /**
     * Scope for specific placement
     */
    public function scopeForPlacement($query, $placement)
    {
        return $query->where('placement', $placement);
    }

    /**
     * Scope for specific ad type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('ad_type', $type);
    }

    /**
     * NEW: Scope for admin priority ordering
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('admin_priority', 'desc')
            ->orderBy('display_order', 'asc');
    }

    /**
     * NEW: Check if ad targets specific user
     */
    public function targetsUser(User $user): bool
    {
        // Check user type targeting
        if (!empty($this->target_user_types)) {
            if (!in_array($user->type, $this->target_user_types) && !in_array('both', $this->target_user_types)) {
                return false;
            }
        }

        // Check excluded user types
        if (!empty($this->exclude_user_types)) {
            if (in_array($user->type, $this->exclude_user_types)) {
                return false;
            }
        }

        // Check region targeting
        if (!empty($this->target_regions)) {
            if (!in_array($user->city, $this->target_regions)) {
                return false;
            }
        }

        // Check excluded regions
        if (!empty($this->exclude_regions)) {
            if (in_array($user->city, $this->exclude_regions)) {
                return false;
            }
        }

        // Check activity level targeting
        if (!empty($this->target_activity_levels)) {
            $userActivityLevel = $this->getUserActivityLevel($user);
            if (!in_array($userActivityLevel, $this->target_activity_levels)) {
                return false;
            }
        }

        // Check order history targeting
        if (!empty($this->target_order_history)) {
            if (!$this->matchesOrderHistory($user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * NEW: Get user activity level
     */
    private function getUserActivityLevel(User $user): string
    {
        $accountAge = $user->created_at->diffInDays(now());
        $orderCount = Order::where('user_id', $user->id)->count();

        if ($accountAge <= 7) {
            return self::ACTIVITY_NEW;
        }

        if ($orderCount >= 50) {
            return self::ACTIVITY_VIP;
        } elseif ($orderCount >= 11) {
            return self::ACTIVITY_HIGH;
        } elseif ($orderCount >= 3) {
            return self::ACTIVITY_MEDIUM;
        } else {
            return self::ACTIVITY_LOW;
        }
    }

    /**
     * NEW: Check if user matches order history criteria
     */
    private function matchesOrderHistory(User $user): bool
    {
        $orderCount = Order::where('user_id', $user->id)->count();
        $completedOrders = Order::where('user_id', $user->id)->where('status', 2)->count();
        $totalSpent = Order::where('user_id', $user->id)->where('status', 2)->sum('price') ?? 0;

        foreach ($this->target_order_history as $criteria) {
            switch ($criteria) {
                case 'no_orders':
                    if ($orderCount == 0) return true;
                    break;
                case 'first_time':
                    if ($orderCount == 1) return true;
                    break;
                case 'repeat_customer':
                    if ($completedOrders >= 2) return true;
                    break;
                case 'high_value':
                    if ($totalSpent >= 500) return true;
                    break;
                case 'low_value':
                    if ($totalSpent < 100) return true;
                    break;
            }
        }

        return false;
    }

    /**
     * Get formatted ad type for display
     */
    public function getFormattedAdTypeAttribute(): string
    {
        return self::getAdTypes()[$this->ad_type] ?? ucfirst($this->ad_type);
    }

    /**
     * Get formatted placement for display
     */
    public function getFormattedPlacementAttribute(): string
    {
        return self::getPlacements()[$this->placement] ?? ucfirst(str_replace('_', ' ', $this->placement));
    }

    /**
     * NEW: Get formatted priority for display
     */
    public function getFormattedPriorityAttribute(): string
    {
        return self::getPriorityLevels()[$this->admin_priority ?? self::PRIORITY_NORMAL] ?? 'Normal Priority';
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->image) {
            return '';
        }

        // Return full URL to the stored image
        return asset('storage/ads/' . $this->image);
    }

    /**
     * Check if ad is currently active and scheduled
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->approval_status !== self::STATUS_APPROVED) {
            return false;
        }

        $now = Carbon::now();

        // Check start date
        if ($this->start_date && $this->start_date->gt($now)) {
            return false;
        }

        // Check end date
        if ($this->end_date && $this->end_date->lt($now)) {
            return false;
        }

        // NEW: Check daily budget
        if ($this->max_daily_budget && $this->daily_spend >= $this->max_daily_budget) {
            return false;
        }

        // NEW: Check performance threshold
        if ($this->auto_pause_enabled && $this->min_ctr_threshold) {
            $currentCtr = $this->view_count > 0 ? ($this->click_count / $this->view_count) * 100 : 0;
            if ($currentCtr < $this->min_ctr_threshold && $this->view_count >= 100) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get ad status for display
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->approval_status === self::STATUS_PENDING) {
            return 'pending_approval';
        }

        if ($this->approval_status === self::STATUS_REJECTED) {
            return 'rejected';
        }

        $now = Carbon::now();

        if ($this->start_date && $this->start_date->gt($now)) {
            return 'scheduled';
        }

        if ($this->end_date && $this->end_date->lt($now)) {
            return 'expired';
        }

        // NEW: Budget exceeded
        if ($this->max_daily_budget && $this->daily_spend >= $this->max_daily_budget) {
            return 'budget_exceeded';
        }

        // NEW: Auto-paused for poor performance
        if ($this->auto_pause_enabled && $this->min_ctr_threshold) {
            $currentCtr = $this->view_count > 0 ? ($this->click_count / $this->view_count) * 100 : 0;
            if ($currentCtr < $this->min_ctr_threshold && $this->view_count >= 100) {
                return 'auto_paused';
            }
        }

        return 'active';
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        switch ($this->status) {
            case 'active':
                return 'status-success';
            case 'scheduled':
                return 'status-info';
            case 'expired':
                return 'status-warning';
            case 'inactive':
                return 'status-danger';
            case 'pending_approval':
                return 'status-warning';
            case 'rejected':
                return 'status-danger';
            case 'budget_exceeded':
                return 'status-warning';
            case 'auto_paused':
                return 'status-warning';
            default:
                return 'status-secondary';
        }
    }

    /**
     * Increment view count
     */
    public function incrementViews(): bool
    {
        return $this->increment('view_count');
    }

    /**
     * Increment click count
     */
    public function incrementClicks(): bool
    {
        return $this->increment('click_count');
    }

    /**
     * NEW: Add to daily spend
     */
    public function addToDailySpend(float $amount): bool
    {
        return $this->increment('daily_spend', $amount);
    }

    /**
     * NEW: Reset daily spend (for daily budget management)
     */
    public function resetDailySpend(): bool
    {
        return $this->update(['daily_spend' => 0]);
    }

    /**
     * NEW: Calculate performance score
     */
    public function calculatePerformanceScore(): float
    {
        $ctr = $this->view_count > 0 ? ($this->click_count / $this->view_count) * 100 : 0;
        $viewScore = min($this->view_count / 100, 10);
        $ageScore = max(0, 10 - $this->created_at->diffInDays(now()) / 30);

        $totalScore = ($ctr * 0.4) + ($viewScore * 0.3) + ($ageScore * 0.3);

        $this->update(['performance_score' => round($totalScore, 2)]);

        return round($totalScore, 2);
    }

    /**
     * NEW: Check if ad should be auto-paused
     */
    public function shouldAutoPause(): bool
    {
        if (!$this->auto_pause_enabled) {
            return false;
        }

        // Check CTR threshold
        if ($this->min_ctr_threshold && $this->view_count >= 100) {
            $currentCtr = ($this->click_count / $this->view_count) * 100;
            if ($currentCtr < $this->min_ctr_threshold) {
                return true;
            }
        }

        // Check daily budget
        if ($this->max_daily_budget && $this->daily_spend >= $this->max_daily_budget) {
            return true;
        }

        return false;
    }

    /**
     * NEW: Get targeting summary for display
     */
    public function getTargetingSummaryAttribute(): array
    {
        $summary = [];

        if (!empty($this->target_user_types)) {
            $summary['user_types'] = $this->target_user_types;
        }

        if (!empty($this->target_regions)) {
            $summary['regions'] = count($this->target_regions) . ' regions';
        }

        if (!empty($this->target_activity_levels)) {
            $summary['activity'] = count($this->target_activity_levels) . ' activity levels';
        }

        if (!empty($this->target_order_history)) {
            $summary['order_history'] = count($this->target_order_history) . ' criteria';
        }

        return $summary;
    }

    /**
     * NEW: Relationships
     */
    public function createdByAdmin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'created_by_admin');
    }

    public function lastModifiedByAdmin()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'last_modified_by_admin');
    }
}
