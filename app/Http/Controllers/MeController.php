<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load('roles');
        $org  = $user->organization;

        return response()->json([
            'id'              => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'organization_id' => $user->organization_id,
            'roles'           => $user->getRoleNames(),
            'organization'    => $org ? [
                'id'             => $org->id,
                'name'           => $org->name,
                'plan'           => $org->plan,
                'business_model' => $org->business_model,
            ] : null,
        ]);
    }

    public function features(Request $request): JsonResponse
    {
        $org = $request->user()->organization;

        if (!$org) {
            return response()->json(['error' => 'organization_not_found'], 404);
        }

        $planConfig = config("plans.{$org->plan}", []);

        $visibleMetrics = array_values(array_intersect(
            $planConfig['metrics'] ?? [],
            config("business_models.{$org->business_model}.metrics", [])
        ));

        return response()->json([
            'plan'           => $org->plan,
            'plan_label'     => $planConfig['label'] ?? $org->plan,
            'business_model' => $org->business_model,
            'modules'        => $planConfig['modules'] ?? [],
            'channels'       => $planConfig['channels'] ?? [],
            'metrics'        => $visibleMetrics,
            'primary_entity' => config("business_models.{$org->business_model}.primary_entity"),
            'limits'         => [
                'max_users'      => $planConfig['max_users'] ?? null,
                'monthly_convos' => $planConfig['monthly_convos'] ?? 0,
            ],
        ]);
    }
}
