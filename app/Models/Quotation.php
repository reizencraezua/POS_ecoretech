<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $primaryKey = 'quotation_id';

    protected $fillable = [
        'quotation_date',
        'notes',
        'terms_and_conditions',
        'status',
        'customer_id',
        'total_amount',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_CLOSED = 'Closed';

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function details()
    {
        return $this->hasMany(QuotationDetail::class, 'quotation_id');
    }

    public function getTotalAmountAttribute()
    {
        // Use stored total_amount if available, otherwise calculate from details
        return $this->attributes['total_amount'] ?? $this->details->sum('subtotal');
    }
}
