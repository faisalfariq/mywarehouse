<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AppLog",
 *     title="AppLog",
 *     description="Application Log model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="action", type="string", example="create"),
 *     @OA\Property(property="module", type="string", example="product"),
 *     @OA\Property(property="ip_address", type="string", example="192.168.1.1"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time"),
 *     @OA\Property(property="user", ref="#/components/schemas/User")
 * )
 */
class AppLog extends Model
{
    protected $table = 'app_logs';
    protected $fillable = [
        'user_id', 
        'action', 
        'module', 
        'ip_address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}