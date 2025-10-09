<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'customer_firstname',
        'customer_middlename',
        'customer_lastname',
        'business_name',
        'customer_address',
        'customer_email',
        'contact_person1',
        'contact_number1',
        'contact_person2',
        'contact_number2',
        'tin',
    ];

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->customer_firstname} {$this->customer_middlename} {$this->customer_lastname}");
    }

    public function getDisplayNameAttribute()
    {
        if ($this->business_name) {
            return $this->full_name . ' - ' . $this->business_name;
        }
        return $this->full_name;
    }
}
