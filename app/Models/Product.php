<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = [
        'product_name',
        'product_code',
        'category_id',
        'unit_id',
        'description'
    ];

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'product_locations')
                    ->withPivot('stock')
                    ->withTimestamps();
    }

    public function mutations()
    {
        return $this->hasMany(Mutation::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
    
    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }
} 