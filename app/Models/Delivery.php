<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TracksHistory;

class Delivery extends Model
{
    use HasFactory, SoftDeletes, TracksHistory;

    protected $primaryKey = 'delivery_id';

    protected $fillable = [
        'order_id',
        'employee_id',
        'delivery_date',
        'delivery_address',
        'driver_name',
        'driver_contact',
        'status',
        'delivery_fee',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivery_fee' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function histories()
    {
        return $this->hasMany(DeliveryHistory::class, 'delivery_id', 'delivery_id');
    }

    /**
     * Generate a unique delivery ID
     */
    public static function generateDeliveryId()
    {
        $lastDelivery = self::orderBy('delivery_id', 'desc')->first();
        $lastId = $lastDelivery ? (int) $lastDelivery->delivery_id : 0;
        return str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }
}
