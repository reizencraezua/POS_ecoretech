<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosition extends Model
{
    use HasFactory;

    protected $primaryKey = 'job_id';
    public $incrementing = true;

    protected $fillable = [
        'job_title',
        'job_description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function employees()
    {
        return $this->hasMany(Employee::class, 'job_id', 'job_id');
    }
}