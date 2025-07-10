<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';
    protected $fillable = [
        'location_code',
        'location_name',
        'address',
        'description'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_locations')
                    ->withPivot('stock')
                    ->withTimestamps();
    }
} 