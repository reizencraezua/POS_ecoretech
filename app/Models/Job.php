<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'job_positions';
    protected $primaryKey = 'job_id';

    protected $fillable = [
        'job_title',
        'job_description',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'job_id');
    }
}
