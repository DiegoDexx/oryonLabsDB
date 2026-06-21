<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    public const PLANS = ['starter', 'pro', 'professional'];
    public const BUSINESS_MODELS = ['project_based', 'subscription', 'transactional', 'appointment'];

    protected $fillable = ['name', 'slug', 'plan', 'business_model', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function hasModule(string $module): bool
    {
        return in_array($module, config("plans.{$this->plan}.modules", []));
    }

    public function hasMetric(string $metric): bool
    {
        $planMetrics  = config("plans.{$this->plan}.metrics", []);
        $modelMetrics = config("business_models.{$this->business_model}.metrics", []);
        return in_array($metric, $planMetrics) && in_array($metric, $modelMetrics);
    }
}
