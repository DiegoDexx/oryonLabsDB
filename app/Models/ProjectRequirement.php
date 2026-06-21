<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectRequirement extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'project_id',
        'field_id',
        'field_value',
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

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function field()
    {
        return $this->belongsTo(ProjectField::class, 'field_id');
    }
}
