<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Subscription;

class MetricsService
{
    public function __construct(private Organization $org) {}

    /**
     * Returns only the metrics visible for this organization
     * (intersection of plan metrics and business_model metrics).
     */
    public function dashboard(): array
    {
        $visible = array_intersect(
            config("plans.{$this->org->plan}.metrics", []),
            config("business_models.{$this->org->business_model}.metrics", [])
        );

        $result = [];
        foreach ($visible as $metric) {
            $method = 'metric' . str_replace('_', '', ucwords($metric, '_'));
            if (method_exists($this, $method)) {
                $result[$metric] = $this->{$method}();
            }
        }
        return $result;
    }

    // ── Common ───────────────────────────────────────────────

    private function metricLeadsTotal(): int
    {
        return Lead::count();
    }

    private function metricLeadsNew(): int
    {
        return Lead::where('status', 'new')->count();
    }

    private function metricConversionBasic(): float
    {
        $total = Lead::count();
        if ($total === 0) return 0.0;
        $converted = Lead::where('status', 'converted')->count();
        return round(($converted / $total) * 100, 1);
    }

    private function metricLeadsByChannel(): array
    {
        return Lead::selectRaw('channel, COUNT(*) as total')
            ->groupBy('channel')
            ->pluck('total', 'channel')
            ->toArray();
    }

    private function metricActivitiesSummary(): array
    {
        return Activity::selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->toArray();
    }

    // ── project_based (reformas, construcción) ───────────────

    private function metricPipelineValue(): float
    {
        return (float) Project::whereNotIn('stage', ['closed', 'lost'])
            ->sum('estimated_value');
    }

    private function metricQuotesRequestedValue(): float
    {
        return (float) Project::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('estimated_value');
    }

    private function metricQuoteToProjectRate(): float
    {
        $quotes = Project::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        if ($quotes === 0) return 0.0;
        $won = Project::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('stage', 'closed')->count();
        return round(($won / $quotes) * 100, 1);
    }

    private function metricAvgProjectValue(): float
    {
        return round((float) Project::where('stage', 'closed')->avg('estimated_value'), 2);
    }

    private function metricProjectsActive(): int
    {
        return Project::whereNotIn('stage', ['closed', 'lost'])->count();
    }

    private function metricProjectsClosed(): int
    {
        return Project::where('stage', 'closed')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
    }

    // ── subscription (academias, mantenimientos) ─────────────

    private function metricMrr(): float
    {
        return (float) Subscription::where('status', 'active')->sum('monthly_fee');
    }

    private function metricChurnRate(): float
    {
        $startOfMonth = now()->startOfMonth();
        $activeStart = Subscription::where('created_at', '<', $startOfMonth)
            ->where('status', 'active')->count();
        if ($activeStart === 0) return 0.0;
        $cancelled = Subscription::where('status', 'cancelled')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        return round(($cancelled / $activeStart) * 100, 1);
    }

    private function metricLtv(): float
    {
        $avgMonthly = (float) Subscription::where('status', 'active')->avg('monthly_fee');
        $churn = $this->metricChurnRate();
        if ($churn <= 0) return round($avgMonthly * 24, 2);
        return round($avgMonthly * (100 / $churn), 2);
    }

    private function metricNewSubscriptions(): int
    {
        return Subscription::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function metricCancelledSubscriptions(): int
    {
        return Subscription::where('status', 'cancelled')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
    }

    // ── transactional (e-commerce, restaurantes) ─────────────

    private function metricTotalRevenue(): float
    {
        return (float) Invoice::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');
    }

    private function metricAvgTicket(): float
    {
        return round((float) Invoice::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->avg('amount'), 2);
    }

    private function metricOrdersCount(): int
    {
        return Invoice::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function metricRepeatCustomerRate(): float
    {
        $total = Client::count();
        if ($total === 0) return 0.0;
        $repeat = Client::has('invoices', '>', 1)->count();
        return round(($repeat / $total) * 100, 1);
    }

    // ── appointment (clínicas, peluquerías) ──────────────────
    // Stubs — no appointments table yet; return 0 until module is built.

    private function metricAppointmentsBooked(): int
    {
        return 0;
    }

    private function metricNoShowRate(): float
    {
        return 0.0;
    }

    private function metricAgendaOccupancy(): float
    {
        return 0.0;
    }

    private function metricRecurringClients(): int
    {
        return 0;
    }
}
