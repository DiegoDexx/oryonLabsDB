<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'organization_id',
        'client_id',
        'project_id',
        'type',
        'description',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('organization', function (Builder $query) {
            if (auth()->check()) {
                $query->where('organization_id', auth()->user()->organization_id);
            }
        });

        static::creating(function ($model) {
            if (empty($model->organization_id)) {
                $model->organization_id = auth()->check()
                    ? auth()->user()->organization_id
                    : 1;
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
