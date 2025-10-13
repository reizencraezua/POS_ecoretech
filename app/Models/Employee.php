<?php

// app/Models/Employee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'employee_firstname',
        'employee_middlename',
        'employee_lastname',
        'employee_email',
        'employee_contact',
        'employee_address',
        'hire_date',
        'job_id',
    ];

    protected $casts = [
        'hire_date' => 'date',
    ];

    /**
     * Get the job position for this employee
     */
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id', 'job_id');
    }

    /**
     * Get orders assigned to this employee
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'employee_id', 'employee_id');
    }

    /**
     * Get deliveries assigned to this employee
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'employee_id', 'employee_id');
    }

    /**
     * Get the user account associated with this employee
     */
    public function user()
    {
        return $this->hasOne(User::class, 'employee_id', 'employee_id');
    }

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute()
    {
        $name = $this->employee_firstname;

        if ($this->employee_middlename) {
            $name .= ' ' . $this->employee_middlename;
        }

        $name .= ' ' . $this->employee_lastname;

        return $name;
    }

    /**
     * Get initials for avatar
     */
    public function getInitialsAttribute()
    {
        return substr($this->employee_firstname, 0, 1) . substr($this->employee_lastname, 0, 1);
    }

    /**
     * Scope for searching employees
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('employee_firstname', 'LIKE', "%{$search}%")
                ->orWhere('employee_lastname', 'LIKE', "%{$search}%")
                ->orWhere('employee_email', 'LIKE', "%{$search}%")
                ->orWhere('employee_contact', 'LIKE', "%{$search}%");
        });
    }
}
