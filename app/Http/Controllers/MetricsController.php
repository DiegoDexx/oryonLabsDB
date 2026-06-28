<?php

namespace App\Http\Controllers;

use App\Models\LlmUsageLog;
use App\Models\Subscription;
use App\Models\VoiceCallLog;
use App\Models\WorkflowExecutionLog;
use App\Services\MetricsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $org = $request->user()->organization;

        if (!$org) {
            return response()->json(['error' => 'organization_not_found'], 404);
        }

        $metrics = (new MetricsService($org))->dashboard();

        return response()->json([
            'plan'           => $org->plan,
            'business_model' => $org->business_model,
            'metrics'        => $metrics,
        ]);
    }

    // ── Integration metrics (cross-tenant — for OryonX admin use) ────────────

    public function usage(Request $request): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|integer',
            'from'            => 'nullable|date',
            'to'              => 'nullable|date',
        ]);

        $orgId = (int) $request->query('organization_id');

        $rows = LlmUsageLog::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->when($request->query('from'), fn (Builder $q, $v) => $q->where('created_at', '>=', $v))
            ->when($request->query('to'),   fn (Builder $q, $v) => $q->where('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->get();

        return response()->json($rows);
    }

    public function calls(Request $request): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|integer',
            'from'            => 'nullable|date',
            'to'              => 'nullable|date',
        ]);

        $orgId = (int) $request->query('organization_id');

        $rows = VoiceCallLog::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->when($request->query('from'), fn (Builder $q, $v) => $q->where('created_at', '>=', $v))
            ->when($request->query('to'),   fn (Builder $q, $v) => $q->where('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->get();

        return response()->json($rows);
    }

    public function executions(Request $request): JsonResponse
    {
        $request->validate([
            'organization_id' => 'required|integer',
            'from'            => 'nullable|date',
            'to'              => 'nullable|date',
        ]);

        $orgId = (int) $request->query('organization_id');

        $rows = WorkflowExecutionLog::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->when($request->query('from'), fn (Builder $q, $v) => $q->where('created_at', '>=', $v))
            ->when($request->query('to'),   fn (Builder $q, $v) => $q->where('created_at', '<=', $v))
            ->orderByDesc('created_at')
            ->get();

        return response()->json($rows);
    }

    public function margin(Request $request): JsonResponse
    {
        $request->validate(['organization_id' => 'required|integer']);

        $orgId = (int) $request->query('organization_id');
        $from  = $request->query('from', now()->startOfMonth()->toDateString());
        $to    = $request->query('to',   now()->endOfMonth()->toDateString());

        $revenue = (float) Subscription::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->where('status', 'active')
            ->sum('monthly_fee');

        $llmCost = (float) LlmUsageLog::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereBetween('created_at', [$from, $to])
            ->sum('cost_usd');

        $voiceCost = (float) VoiceCallLog::withoutGlobalScopes()
            ->where('organization_id', $orgId)
            ->whereBetween('created_at', [$from, $to])
            ->sum('cost_usd');

        $aiCost    = round($llmCost + $voiceCost, 4);
        $marginUsd = round($revenue - $aiCost, 4);
        $marginPct = $revenue > 0 ? round(($marginUsd / $revenue) * 100, 2) : 0.0;

        return response()->json([
            'organization_id' => $orgId,
            'period'          => ['from' => $from, 'to' => $to],
            'revenue'         => $revenue,
            'ai_cost'         => $aiCost,
            'margin_usd'      => $marginUsd,
            'margin_pct'      => $marginPct,
        ]);
    }
}
