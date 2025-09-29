<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $primaryKey = 'delivery_id';

    protected $fillable = [
        'order_id',
        'delivery_date',
        'delivery_address',
        'driver_name',
        'driver_contact',
        'status',
        'delivery_fee',
        'notes',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_fee' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
