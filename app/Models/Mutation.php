<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Mutation",
 *     title="Mutation",
 *     description="Mutation model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=1),
 *     @OA\Property(property="location_id", type="integer", example=1),
 *     @OA\Property(property="date", type="string", format="date", example="2024-01-01"),
 *     @OA\Property(property="mutation_type", type="string", enum={"in", "out"}, example="in"),
 *     @OA\Property(property="quantity", type="integer", example=10),
 *     @OA\Property(property="note", type="string", example="Stock in from supplier"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product"),
 *     @OA\Property(property="location", ref="#/components/schemas/Location")
 * )
 */
class Mutation extends Model
{
    protected $table = 'mutations';
    protected $fillable = [
        'user_id',
        'product_id',
        'location_id',
        'date',
        'mutation_type',
        'quantity',
        'note'
    ];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
} 