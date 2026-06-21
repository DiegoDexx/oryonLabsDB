<?php

namespace App\Http\Controllers;

use App\Services\MetricsService;
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
}
