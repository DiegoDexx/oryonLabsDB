<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'organization_id',
        'client_id',
        'plan',
        'setup_fee',
        'monthly_fee',
        'status',
        'start_date',
        'next_billing_date',
        'notes',
    ];

    protected $casts = [
        'start_date'         => 'date',
        'next_billing_date'  => 'date',
        'setup_fee'          => 'decimal:2',
        'monthly_fee'        => 'decimal:2',
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

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
