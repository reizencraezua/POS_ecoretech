<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_detail_id';

    protected $fillable = [
        'order_id',
        'product_id',
        'service_id',
        'quantity',
        'unit_id',
        'size',
        'price',
        'subtotal',
        'vat',
        'discount',
        'layout',
        'layout_price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'vat' => 'decimal:2',
        'discount' => 'decimal:2',
        'layout' => 'boolean',
        'layout_price' => 'decimal:2',
    ];

    /**
     * Get the order that owns the detail
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Get the product for this detail
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the service for this detail
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Get the unit for this detail
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the item name (product or service)
     */
    public function getItemNameAttribute()
    {
        if ($this->product) {
            return $this->product->product_name;
        }

        if ($this->service) {
            return $this->service->service_name;
        }

        return 'Unknown Item';
    }

    /**
     * Get the item type
     */
    public function getItemTypeAttribute()
    {
        if ($this->product_id) {
            return 'Product';
        }

        if ($this->service_id) {
            return 'Service';
        }

        return 'Unknown';
    }
}
