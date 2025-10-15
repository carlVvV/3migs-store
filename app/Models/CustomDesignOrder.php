<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomDesignOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'total_amount',
        'currency',
        'fabric',
        'color',
        'embroidery',
        'quantity',
        'measurements',
        'fabric_yardage',
        'reference_image',
        'pricing',
        'additional_notes',
        'billing_address',
        'shipping_address',
        'transaction_id',
        'paid_at',
        'payment_provider',
        'estimated_completion_date',
        'admin_notes',
        'assigned_to',
    ];

    protected $casts = [
        'measurements' => 'array',
        'pricing' => 'array',
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'total_amount' => 'decimal:2',
        'fabric_yardage' => 'decimal:2',
        'paid_at' => 'datetime',
        'estimated_completion_date' => 'datetime',
    ];

    /**
     * Get the user that owns the custom design order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'CD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    /**
     * Get the payment status badge color.
     */
    public function getPaymentStatusBadgeColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'yellow',
            'paid' => 'green',
            'failed' => 'red',
            'refunded' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Scope for pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for processing orders.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope for completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for paid orders.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope for orders by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted measurements.
     */
    public function getFormattedMeasurementsAttribute(): string
    {
        if (!$this->measurements) {
            return 'N/A';
        }

        $measurements = $this->measurements;
        return sprintf(
            'Chest: %s", Waist: %s", Length: %s", Shoulder: %s", Sleeve: %s"',
            $measurements['chest'] ?? 'N/A',
            $measurements['waist'] ?? 'N/A',
            $measurements['length'] ?? 'N/A',
            $measurements['shoulder_width'] ?? 'N/A',
            $measurements['sleeve_length'] ?? 'N/A'
        );
    }

    /**
     * Get formatted pricing breakdown.
     */
    public function getFormattedPricingAttribute(): array
    {
        if (!$this->pricing) {
            return [];
        }

        return [
            'fabric_cost' => '₱' . number_format($this->pricing['fabricCost'] ?? 0, 2),
            'embroidery_cost' => '₱' . number_format($this->pricing['embroideryCost'] ?? 0, 2),
            'total_cost' => '₱' . number_format($this->pricing['totalCost'] ?? 0, 2),
        ];
    }
}