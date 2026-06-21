<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'organization_id',
        'client_id',
        'subscription_id',
        'invoice_number',
        'amount',
        'type',
        'status',
        'due_date',
        'paid_date',
    ];

    protected $casts = [
        'due_date'  => 'date',
        'paid_date' => 'date',
        'amount'    => 'decimal:2',
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

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
