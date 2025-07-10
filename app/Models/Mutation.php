<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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