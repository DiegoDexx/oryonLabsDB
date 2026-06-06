<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'company', 'email', 'phone', 'status'];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
