<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Client;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadLookupController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $email = trim((string) $request->query('email', ''));
        $phone = PhoneNormalizer::normalize((string) $request->query('phone', '')) ?? '';

        if (empty($email) && empty($phone)) {
            return response()->json(['exists' => false], 200);
        }

        // 1. Clientes primero — prioridad máxima, ya convirtieron
        $client = Client::select(['id', 'name', 'company', 'email', 'phone', 'status', 'language'])
            ->where(function ($q) use ($email, $phone) {
                if ($email) $q->orWhere('email', $email);
                if ($phone) $q->orWhere('phone', $phone);
            })
            ->first();

        if ($client) {
            return response()->json([
                'exists'   => true,
                'type'     => 'client',
                'name'     => $client->name,
                'company'  => $client->company,
                'status'   => $client->status,
                'language' => $client->language,
            ], 200);
        }

        // 2. Leads — el más reciente si hay varios (el merge los unificará a futuro)
        $lead = Lead::select([
                'id', 'name', 'company', 'challenge', 'client_volume',
                'suggested_plan', 'commercial_summary', 'status', 'language',
            ])
            ->where(function ($q) use ($email, $phone) {
                if ($email) $q->orWhere('email', $email);
                if ($phone) $q->orWhere('phone', $phone);
            })
            ->latest('id')
            ->first();

        if (!$lead) {
            return response()->json(['exists' => false], 200);
        }

        return response()->json([
            'exists'             => true,
            'type'               => 'lead',
            'lead_id'            => $lead->id,
            'name'               => $lead->name,
            'company'            => $lead->company,
            'challenge'          => $lead->challenge,
            'client_volume'      => $lead->client_volume,
            'suggested_plan'     => $lead->suggested_plan,
            'commercial_summary' => $lead->commercial_summary,
            'status'             => $lead->status,
            'language'           => $lead->language,
        ], 200);
    }

    public function mergeUpdate(Request $request, int $id): JsonResponse
    {
        $lead = Lead::find($id);

        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }

        $mergeableFields = [
            'name', 'company', 'email', 'phone', 'challenge',
            'client_volume', 'tools', 'urgency', 'suggested_plan',
            'commercial_summary', 'category', 'project_name',
            'priority', 'next_action', 'tags',
        ];

        $data    = $request->only($mergeableFields);
        $updated = false;

        foreach ($data as $key => $value) {
            if ($value === null || $value === '') continue;

            if ($key === 'phone') {
                $value = PhoneNormalizer::normalize($value);
                if ($value === null) continue;
            }

            if (empty($lead->$key) || $lead->$key !== $value) {
                $lead->$key = $value;
                $updated    = true;
            }
        }

        if ($updated) {
            $lead->last_contacted_at = now();
            $lead->save();
        }

        return response()->json([
            'updated' => $updated,
            'lead'    => $lead->only($mergeableFields),
        ], 200);
    }
}
