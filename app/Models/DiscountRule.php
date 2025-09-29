<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'discount_rules';
    protected $primaryKey = 'rule_id';

    protected $fillable = [
        'rule_name',
        'description',
        'min_quantity',
        'max_quantity',
        'discount_percentage',
        'discount_amount',
        'discount_type',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    /**
     * Check if the rule is valid for a given quantity
     */
    public function isValidForQuantity($quantity)
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->toDateString();

        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }

        if ($quantity < $this->min_quantity) {
            return false;
        }

        if ($this->max_quantity && $quantity > $this->max_quantity) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for a given subtotal and quantity
     */
    public function calculateDiscount($subtotal, $quantity)
    {
        if (!$this->isValidForQuantity($quantity)) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            return $subtotal * ($this->discount_percentage / 100);
        }

        return $this->discount_amount;
    }

    /**
     * Scope for active rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for rules valid at a given date
     */
    public function scopeValidAt($query, $date = null)
    {
        $date = $date ?: now()->toDateString();

        return $query->where(function ($q) use ($date) {
            $q->whereNull('valid_from')
                ->orWhere('valid_from', '<=', $date);
        })->where(function ($q) use ($date) {
            $q->whereNull('valid_until')
                ->orWhere('valid_until', '>=', $date);
        });
    }
}
