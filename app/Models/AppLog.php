<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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