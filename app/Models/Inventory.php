<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'inventory_id',
        'name',
        'description',
        'stocks',
        'stock_in',
        'critical_level',
        'supplier_id',
        'unit',
        'last_updated',
        'is_active'
    ];

    protected $casts = [
        'last_updated' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the supplier that owns the inventory.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    /**
     * Get the stock usages for the inventory.
     */
    public function stockUsages(): HasMany
    {
        return $this->hasMany(StockUsage::class);
    }

    /**
     * Check if inventory is at critical level.
     */
    public function isCriticalLevel(): bool
    {
        return $this->stocks <= $this->critical_level;
    }

    /**
     * Get the status of the inventory.
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        
        if ($this->isCriticalLevel()) {
            return 'Critical';
        }
        
        return 'Normal';
    }

    /**
     * Generate unique inventory ID.
     */
    public static function generateInventoryId(): string
    {
        $lastInventory = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastInventory ? $lastInventory->id + 1 : 1;
        return 'INV-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update stock balance and last updated timestamp.
     */
    public function updateStock(int $quantity, string $operation = 'add'): void
    {
        if ($operation === 'add') {
            $this->stocks += $quantity;
            $this->stock_in += $quantity;
        } else {
            $this->stocks = max(0, $this->stocks - $quantity);
        }
        
        $this->last_updated = now();
        $this->save();
    }
}
