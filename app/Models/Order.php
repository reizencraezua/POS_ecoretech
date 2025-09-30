<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_date',
        'deadline_date',
        'order_status',
        'total_amount',
        'layout_design_fee',
        'customer_id',
        'employee_id',
        'layout_employee_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'deadline_date' => 'date',
        'total_amount' => 'decimal:2',
        'layout_design_fee' => 'decimal:2',
    ];

    const STATUS_ON_PROCESS = 'On-Process';
    const STATUS_DESIGNING = 'Designing';
    const STATUS_PRODUCTION = 'Production';
    const STATUS_FOR_RELEASING = 'For Releasing';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_CANCELLED = 'Cancelled';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function layoutEmployee()
    {
        return $this->belongsTo(Employee::class, 'layout_employee_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'order_id');
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount_paid');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->final_total_amount - $this->total_paid;
    }

    // New calculation methods following the formula
    public function getTotalAmountAttribute()
    {
        // Formula 1: Total Amount = (Quantity × Unit Price)
        return $this->details->sum(function ($detail) {
            return $detail->quantity * $detail->price;
        });
    }

    public function getSubTotalAttribute()
    {
        // Formula 2: Sub Total = Total Amount ÷ 1.12
        return $this->total_amount / 1.12;
    }

    public function getVATAmountAttribute()
    {
        // Formula 3: VAT Tax = Total Amount × 0.12
        return $this->total_amount * 0.12;
    }

    public function getOrderDiscountAmountAttribute()
    {
        // Formula 4: Discount Amount = Total Amount × Discount Rate
        $totalQuantity = $this->details->sum('quantity');
        $totalAmount = $this->total_amount;
        
        // Get discount rules (you may need to adjust this based on your discount rules implementation)
        $discountRules = \App\Models\DiscountRule::all();
        
        foreach ($discountRules as $rule) {
            if ($totalQuantity >= $rule->min_quantity && 
                ($rule->max_quantity === null || $totalQuantity <= $rule->max_quantity)) {
                if ($rule->discount_type === 'percentage') {
                    return $totalAmount * ($rule->discount_percentage / 100);
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
        // Formula 5: Final Total Amount = (Total Amount - Discount Amount) + layout fee
        $totalAmount = $this->total_amount;
        $discountAmount = $this->order_discount_amount;
        $layoutFees = $this->layout_fees;
        
        return ($totalAmount - $discountAmount) + $layoutFees;
    }
}
