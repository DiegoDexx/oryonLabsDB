<?php

namespace App\Models;

use App\Support\PhoneNormalizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    // Search via client relation — Subscription has no direct email/phone/name columns

    public static function searchByEmail(string $email): Collection
    {
        return static::with('client')->whereHas('client', fn ($q) => $q->where('email', trim($email)))->get();
    }

    public static function searchByPhone(string $phone): Collection
    {
        $normalized = PhoneNormalizer::normalize($phone);
        if (!$normalized) return collect();

        return static::with('client')->whereHas('client', fn ($q) => $q->where('phone', $normalized))->get();
    }

    public static function searchByName(string $name): Collection
    {
        $op = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
        return static::with('client')->whereHas('client', fn ($q) => $q->where('name', $op, '%' . trim($name) . '%'))->get();
    }
}
