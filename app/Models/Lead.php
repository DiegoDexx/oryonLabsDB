<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes, Searchable;

    protected $fillable = [
        'organization_id',
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
        'language',
        'notes',
        'lead_score',
        'next_action',
        'tags',
        'utm_source',
        'utm_campaign',
        'last_contacted_at',
        'assigned_to',
        'client_id',
        'project_id',
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

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
