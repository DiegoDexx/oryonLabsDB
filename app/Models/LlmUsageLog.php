<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LlmUsageLog extends Model
{
    protected $fillable = [
        'organization_id',
        'lead_id',
        'generation_id',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'cost_usd',
        'channel',
    ];

    protected $casts = [
        'cost_usd' => 'decimal:6',
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
                    : null;
            }
        });
    }
}
