<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayoutFeeSetting extends Model
{
    use HasFactory;

    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'setting_name',
        'layout_fee_amount',
        'layout_fee_type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'layout_fee_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public static function getActiveSetting()
    {
        return self::where('is_active', true)->first();
    }

    public function calculateFee($subtotal = 0)
    {
        if ($this->layout_fee_type === 'percentage') {
            return ($subtotal * $this->layout_fee_amount) / 100;
        }

        return $this->layout_fee_amount;
    }
}
