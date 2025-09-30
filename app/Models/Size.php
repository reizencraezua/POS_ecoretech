<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Size extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'size_id';

    protected $fillable = [
        'size_name',
        'size_value',
        'size_group',
        'unit_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'size_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'size_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_size', 'size_id', 'category_id');
    }
}
