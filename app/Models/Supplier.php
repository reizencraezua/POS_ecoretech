<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'supplier_id';

    protected $fillable = [
        'supplier_name',
        'supplier_email',
        'supplier_contact',
        'supplier_address',
    ];

    // Products are related through inventories, not directly
    // public function products()
    // {
    //     return $this->hasManyThrough(Product::class, Inventory::class, 'supplier_id', 'product_id', 'supplier_id', 'product_id');
    // }

    public function orders()
    {
        return $this->hasMany(Order::class, 'supplier_id');
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'supplier_id');
    }

    public function getCompanyNameAttribute()
    {
        return $this->supplier_name;
    }
}
