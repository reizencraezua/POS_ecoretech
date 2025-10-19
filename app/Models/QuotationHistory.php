<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationHistory extends Model
{
    protected $primaryKey = 'quotation_history_id';
    
    protected $fillable = [
        'quotation_id',
        'action',
        'old_values',
        'new_values',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the quotation that owns the history
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'quotation_id');
    }

    /**
     * Get the user who made the update
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
