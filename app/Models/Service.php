<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'services';
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'service_name',
        'description',
        'base_fee',
        'layout_price',
        'requires_layout',
        'category_id',
        'size_id',
        'unit_id',
    ];

    protected $casts = [
        'base_fee' => 'decimal:2',
        'layout_price' => 'decimal:2',
        'requires_layout' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class, 'size_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
