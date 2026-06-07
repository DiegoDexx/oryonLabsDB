<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Client;
use App\Models\Project;
use App\Models\Activity;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        return Lead::with(['client', 'project'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function show(Lead $lead)
    {
        return $lead->load(['client', 'project']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'company'            => 'nullable|string|max:255',
            'email'              => 'nullable|email|max:255',
            'phone'              => 'required|string|max:20',
            'channel'            => 'nullable|string|max:50',
            'challenge'          => 'nullable|string',
            'client_volume'      => 'nullable|string|max:50',
            'tools'              => 'nullable|string',
            'urgency'            => 'nullable|string|max:100',
            'category'           => 'nullable|string|max:100',
            'project_name'       => 'nullable|string|max:255',
            'priority'           => 'nullable|in:low,medium,high',
            'suggested_plan'     => 'nullable|string|max:50',
            'commercial_summary' => 'nullable|string',
        ]);

        $lead = Lead::create([
            ...$validated,
            'status'   => 'new',
            'channel'  => $validated['channel']  ?? 'form',
            'priority' => $validated['priority'] ?? 'medium',
        ]);

        return response()->json([
            'success' => true,
            'lead'    => $lead,
        ], 201);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,qualified,converted,discarded',
        ]);
        $lead->update(['status' => $request->status]);
        return response()->json($lead);
    }

    public function updateNotes(Request $request, Lead $lead)
    {
        $request->validate(['notes' => 'nullable|string']);
        $lead->update(['notes' => $request->notes]);
        return response()->json($lead);
    }

    public function convert(Request $request, Lead $lead)
    {
        $client = Client::updateOrCreate(
            ['phone' => $lead->phone],
            [
                'name'    => $lead->name,
                'email'   => $lead->email,
                'company' => $lead->company,
                'status'  => 'active',
            ]
        );

        $project = Project::create([
            'name'               => $lead->project_name ?? "Project: {$lead->name}",
            'category'           => $lead->category     ?? 'other',
            'client_id'          => $client->id,
            'stage'              => 'onboarding',
            'priority'           => $lead->priority     ?? 'medium',
            'resumen_comercial'  => $lead->commercial_summary,
            'canal'              => $lead->channel,
        ]);

        Activity::create([
            'client_id'   => $client->id,
            'project_id'  => $project->id,
            'type'        => 'system',
            'description' => "Lead converted to client from channel: {$lead->channel}",
        ]);

        $lead->update([
            'status'     => 'converted',
            'client_id'  => $client->id,
            'project_id' => $project->id,
        ]);

        return response()->json([
            'success' => true,
            'client'  => $client,
            'project' => $project,
            'lead'    => $lead,
        ], 201);
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return response()->json(null, 204);
    }
}
