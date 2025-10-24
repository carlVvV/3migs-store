<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LowStockNotification extends Model
{
    protected $fillable = [
        'product_id',
        'product_name',
        'product_sku',
        'current_stock',
        'threshold',
        'is_resolved',
        'notified_at'
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'is_resolved' => 'boolean'
    ];

    /**
     * Get the product that owns the notification
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(BarongProduct::class, 'product_id');
    }

    /**
     * Scope for unresolved notifications
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope for recent notifications
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('notified_at', '>=', now()->subDays($days));
    }

    /**
     * Mark notification as resolved
     */
    public function markAsResolved()
    {
        $this->update(['is_resolved' => true]);
    }
}
