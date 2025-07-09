<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'text',
        'data_type',
        'data',
        'is_new',
        'is_read',
        'read_at',
        'seen_at'
    ];

    protected $casts = [
        'is_new' => 'boolean',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'seen_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related order if data_type is 'order'
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'data', 'id');
    }

    /**
     * Mark notification as seen (when user enters notifications screen)
     */
    public function markAsSeen(): void
    {
        if ($this->is_new) {
            $this->update([
                'is_new' => false,
                'seen_at' => now()
            ]);
        }
    }

    /**
     * Mark notification as read (when user explicitly marks as read)
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_new' => false,
            'is_read' => true,
            'seen_at' => $this->seen_at ?? now(),
            'read_at' => now()
        ]);
    }

    /**
     * Scope for new notifications
     */
    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}
