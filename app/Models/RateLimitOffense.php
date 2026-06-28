<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateLimitOffense extends Model
{
    protected $fillable = [
        'phone',
        'violation_count',
        'blocked_until',
        'last_violation_at',
    ];

    protected $casts = [
        'blocked_until'    => 'datetime',
        'last_violation_at' => 'datetime',
    ];
}
