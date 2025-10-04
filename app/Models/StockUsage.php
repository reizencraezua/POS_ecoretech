<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockUsage extends Model
{
    protected $fillable = [
        'inventory_id',
        'quantity_used',
        'purpose',
        'used_by',
        'used_at'
    ];

    protected $casts = [
        'used_at' => 'datetime'
    ];

    /**
     * Get the inventory that owns the stock usage.
     */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
