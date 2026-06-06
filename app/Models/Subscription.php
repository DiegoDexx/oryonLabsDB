<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'client_id',
        'plan',
        'setup_fee',
        'monthly_fee',
        'status',
        'start_date',
        'next_billing_date',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_billing_date' => 'date',
        'setup_fee' => 'decimal:2',
        'monthly_fee' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
