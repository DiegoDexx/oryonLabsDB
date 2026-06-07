<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'company',
        'email',
        'phone',
        'channel',
        'challenge',
        'client_volume',
        'tools',
        'urgency',
        'category',
        'project_name',
        'priority',
        'suggested_plan',
        'commercial_summary',
        'status',
        'notes',
        'client_id',
        'project_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
