<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TracksHistory;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, TracksHistory;

    protected $primaryKey = 'quotation_id';

    protected $fillable = [
        'quotation_date',
        'valid_until',
        'notes',
        'terms_and_conditions',
        'status',
        'customer_id',
        'total_amount',
        'created_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
        'total_amount' => 'decimal:2',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_APPROVED = 'Approved';
    const STATUS_CLOSED = 'Closed';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function details()
    {
        return $this->hasMany(QuotationDetail::class, 'quotation_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories()
    {
        return $this->hasMany(QuotationHistory::class, 'quotation_id', 'quotation_id');
    }

    // Calculation methods following the same formula as orders
    public function getTotalAmountAttribute()
    {
        // Formula 1: Total Amount = (Quantity × Unit Price)
        return $this->details->sum(function ($detail) {
            return $detail->quantity * $detail->price;
        });
    }

    public function getSubTotalAttribute()
    {
        // Formula 1: Sub Total = (Quantity × Unit Price)
        return $this->details->sum(function ($detail) {
            return $detail->quantity * $detail->price;
        });
    }

    public function getVATAmountAttribute()
    {
        // Formula 2: VAT Tax = Sub Total × 0.12
        return $this->sub_total * 0.12;
    }

    public function getBaseAmountAttribute()
    {
        // Formula 3: Base Amount = Sub Total - VAT
        return $this->sub_total - $this->vat_amount;
    }

    public function getQuotationDiscountAmountAttribute()
    {
        // Formula 4: Discount Amount = Sub Total × Discount Rate
        $totalQuantity = $this->details->sum('quantity');
        $subTotal = $this->sub_total;
        
        // Get discount rules
        $discountRules = \App\Models\DiscountRule::all();
        
        foreach ($discountRules as $rule) {
            if ($totalQuantity >= $rule->min_quantity && 
                ($rule->max_quantity === null || $totalQuantity <= $rule->max_quantity)) {
                if ($rule->discount_type === 'percentage') {
                    return $subTotal * ($rule->discount_percentage / 100);
                } else {
                    return $rule->discount_amount;
                }
            }
        }
        return 0;
    }

    public function getLayoutFeesAttribute()
    {
        return $this->details->sum(function ($detail) {
            return $detail->layout ? $detail->layout_price : 0;
        });
    }

    public function getFinalTotalAmountAttribute()
    {
        // Formula 5: Final Total Amount = (Sub Total - Discount Amount) + layout fee
        $subTotal = $this->sub_total;
        $discountAmount = $this->quotation_discount_amount;
        $layoutFees = $this->layout_fees;
        
        return ($subTotal - $discountAmount) + $layoutFees;
    }

    public function getQuotationDiscountInfoAttribute()
    {
        $totalQuantity = $this->details->sum('quantity');
        $subTotal = $this->sub_total;
        
        $discountRules = \App\Models\DiscountRule::active()->validAt()->orderBy('min_quantity')->get();
        
        foreach ($discountRules as $rule) {
            if ($totalQuantity >= $rule->min_quantity && 
                ($rule->max_quantity === null || $totalQuantity <= $rule->max_quantity)) {
                return [
                    'type' => $rule->discount_type,
                    'percentage' => $rule->discount_percentage,
                    'amount' => $rule->discount_amount,
                    'rule_name' => $rule->rule_name,
                    'description' => $rule->description,
                ];
            }
        }
        return null;
    }

    /**
     * Check if this quotation has been converted to an order with payments
     * Since there's no direct relationship, we check for orders with the same customer
     * and similar total amount that have payments
     */
    public function hasPayments()
    {
        // If quotation is not closed, it can't have payments
        if ($this->status !== 'Closed') {
            return false;
        }

        // Look for orders with the same customer and similar total amount
        $orders = \App\Models\Order::where('customer_id', $this->customer_id)
            ->where('total_amount', '>=', $this->final_total_amount * 0.95) // Allow 5% variance
            ->where('total_amount', '<=', $this->final_total_amount * 1.05) // Allow 5% variance
            ->where('order_date', '>=', $this->quotation_date) // Order created after quotation
            ->get();

        // Check if any of these orders have payments
        foreach ($orders as $order) {
            if ($order->payments()->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate a unique quotation ID
     */
    public static function generateQuotationId()
    {
        $lastQuotation = self::orderBy('quotation_id', 'desc')->first();
        $lastId = $lastQuotation ? (int) $lastQuotation->quotation_id : 0;
        return str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }
}
