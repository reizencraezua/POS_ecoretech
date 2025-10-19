<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_type',
        'transaction_id',
        'transaction_name',
        'action',
        'edited_by',
        'user_id',
        'changes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Transaction type constants
    const TYPE_QUOTATION = 'quotation';
    const TYPE_ORDER = 'order';
    const TYPE_PAYMENT = 'payment';
    const TYPE_DELIVERY = 'delivery';

    // Action constants
    const ACTION_CREATED = 'created';
    const ACTION_UPDATED = 'updated';
    const ACTION_DELETED = 'deleted';
    const ACTION_STATUS_CHANGED = 'status_changed';
    const ACTION_CONVERTED_TO_ORDER = 'converted_to_order';

    /**
     * Get the user who made the change
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction name based on type
     */
    public function getTransactionNameAttribute($value)
    {
        // If we have a stored transaction name, use it (this takes priority)
        if ($value) {
            return $value;
        }

        // For payments, try to get receipt number from changes data as fallback
        if ($this->transaction_type === self::TYPE_PAYMENT) {
            if ($this->changes && isset($this->changes['created']['receipt_number'])) {
                return 'Payment - ' . $this->changes['created']['receipt_number'];
            } elseif ($this->changes && isset($this->changes['updated']['receipt_number'])) {
                return 'Payment - ' . $this->changes['updated']['receipt_number'];
            } else {
                // Fallback to payment ID if receipt number not found
                return 'Payment #' . $this->transaction_id;
            }
        }

        // Generate transaction name based on type and ID
        switch ($this->transaction_type) {
            case self::TYPE_QUOTATION:
                return 'Quotation #' . str_pad($this->transaction_id, 5, '0', STR_PAD_LEFT);
            case self::TYPE_ORDER:
                return 'Job Order #' . str_pad($this->transaction_id, 5, '0', STR_PAD_LEFT);
            case self::TYPE_DELIVERY:
                return 'Delivery #' . str_pad($this->transaction_id, 5, '0', STR_PAD_LEFT);
            default:
                return 'Transaction #' . $this->transaction_id;
        }
    }

    /**
     * Get the display name for the transaction (used in logs view)
     */
    public function getDisplayNameAttribute()
    {
        // For payments, always try to get receipt number from changes data
        if ($this->transaction_type === self::TYPE_PAYMENT) {
            if ($this->changes && isset($this->changes['created']['receipt_number'])) {
                return 'Payment - ' . $this->changes['created']['receipt_number'];
            } elseif ($this->changes && isset($this->changes['updated']['receipt_number'])) {
                return 'Payment - ' . $this->changes['updated']['receipt_number'];
            } else {
                // Fallback to payment ID if receipt number not found
                return 'Payment #' . $this->transaction_id;
            }
        }

        // For other transaction types, use the transaction_name
        return $this->transaction_name;
    }

    /**
     * Get formatted transaction ID
     */
    public function getFormattedTransactionIdAttribute()
    {
        switch ($this->transaction_type) {
            case self::TYPE_QUOTATION:
                return 'QUOTE-' . str_pad($this->transaction_id, 5, '0', STR_PAD_LEFT);
            case self::TYPE_ORDER:
                return 'ORDER-' . str_pad($this->transaction_id, 5, '0', STR_PAD_LEFT);
            case self::TYPE_PAYMENT:
                return 'RECEIPT-' . str_pad($this->transaction_id, 5, '0', STR_PAD_LEFT);
            case self::TYPE_DELIVERY:
                return 'DELIVERY-' . str_pad($this->transaction_id, 5, '0', STR_PAD_LEFT);
            default:
                return $this->transaction_id;
        }
    }

    /**
     * Get the transaction type label
     */
    public function getTransactionTypeLabelAttribute()
    {
        switch ($this->transaction_type) {
            case self::TYPE_QUOTATION:
                return 'Quotation';
            case self::TYPE_ORDER:
                return 'Job Order';
            case self::TYPE_PAYMENT:
                return 'Payment';
            case self::TYPE_DELIVERY:
                return 'Delivery';
            default:
                return ucfirst($this->transaction_type);
        }
    }

    /**
     * Get the action label
     */
    public function getActionLabelAttribute()
    {
        switch ($this->action) {
            case self::ACTION_CREATED:
                return 'Created';
            case self::ACTION_UPDATED:
                return 'Updated';
            case self::ACTION_DELETED:
                return 'Deleted';
            case self::ACTION_STATUS_CHANGED:
                return 'Status Changed';
            default:
                return ucfirst($this->action);
        }
    }

    /**
     * Get formatted date and time
     */
    public function getFormattedDateTimeAttribute()
    {
        return $this->created_at->format('M d, Y - h:i A');
    }

    /**
     * Scope for filtering by transaction type
     */
    public function scopeByTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
