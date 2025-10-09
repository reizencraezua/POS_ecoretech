<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'receipt_number',
        'payment_date',
        'payment_method',
        'payment_term',
        'amount_paid',
        'change',
        'balance',
        'reference_number',
        'remarks',
        'order_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
        'change' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public const METHOD_CASH = 'Cash';
    public const METHOD_GCASH = 'GCash';
    public const METHOD_BANK_TRANSFER = 'Bank Transfer';
    public const METHOD_CHECK = 'Check';
    public const METHOD_CREDIT_CARD = 'Credit Card';

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the order including soft-deleted ones
     */
    public function orderWithTrashed()
    {
        return $this->belongsTo(Order::class, 'order_id')->withTrashed();
    }

    /**
     * Check if the payment has a valid order (including soft-deleted)
     */
    public function hasValidOrder()
    {
        return $this->orderWithTrashed()->exists();
    }

    /**
     * Get the order status (active, deleted, or not found)
     */
    public function getOrderStatus()
    {
        if (!$this->hasValidOrder()) {
            return 'not_found';
        }
        
        $order = $this->orderWithTrashed()->first();
        return $order->trashed() ? 'deleted' : 'active';
    }
}
