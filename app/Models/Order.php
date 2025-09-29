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
        return $this->total_amount - $this->total_paid;
    }
}
