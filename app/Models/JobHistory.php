<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobHistory extends Model
{
    protected $primaryKey = 'job_history_id';
    
    protected $fillable = [
        'job_id',
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
     * Get the job that owns the history
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id', 'job_id');
    }

    /**
     * Get the user who made the update
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
