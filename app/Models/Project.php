<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'category',
        'client_id',
        'stage',
        'priority',
        'estimated_delivery',
        'commercial_summary',
        'channel',
        'estimated_value',
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

    public function requirements()
    {
        return $this->hasMany(ProjectRequirement::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
