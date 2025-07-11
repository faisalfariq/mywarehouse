<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Location",
 *     title="Location",
 *     description="Location model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="location_code", type="string", example="WH001"),
 *     @OA\Property(property="location_name", type="string", example="Warehouse A"),
 *     @OA\Property(property="address", type="string", example="Jl. Sudirman No. 123, Jakarta"),
 *     @OA\Property(property="description", type="string", example="Main warehouse for electronics"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
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