<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name', 'category', 'client_id', 'stage', 'priority', 'estimated_delivery', 'resumen_comercial', 'canal'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function requirements()
    {
        return $this->hasMany(ProjectRequirement::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
