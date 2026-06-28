<?php

namespace App\Models;

use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    // Search via client relation — Invoice has no direct email/phone/name columns

    public static function searchByEmail(string $email): Collection
    {
        return static::whereHas('client', fn ($q) => $q->where('email', trim($email)))->get();
    }

    public static function searchByPhone(string $phone): Collection
    {
        $normalized = PhoneNormalizer::normalize($phone);
        if (!$normalized) return collect();

        return static::whereHas('client', fn ($q) => $q->where('phone', $normalized))->get();
    }

    public static function searchByName(string $name): Collection
    {
        return static::whereHas('client', fn ($q) => $q->where('name', 'ILIKE', '%' . trim($name) . '%'))->get();
    }
}
