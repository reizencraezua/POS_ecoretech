<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'unit_id';

    protected $fillable = [
        'unit_name',
        'unit_code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'unit_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'unit_id');
    }

    public function sizes()
    {
        return $this->hasMany(Size::class, 'unit_id');
    }
}
