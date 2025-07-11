<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Product",
 *     title="Product",
 *     description="Product model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product_name", type="string", example="Laptop ASUS"),
 *     @OA\Property(property="product_code", type="string", example="LAP001"),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="unit_id", type="integer", example=1),
 *     @OA\Property(property="description", type="string", example="High performance laptop"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="category", ref="#/components/schemas/ProductCategory"),
 *     @OA\Property(property="unit", ref="#/components/schemas/ProductUnit")
 * )
 */
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