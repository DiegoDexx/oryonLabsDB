<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Client;
use App\Models\Project;
use App\Models\Activity;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Lead::with(['client', 'project'])
            ->when($request->query('status'),      fn($q, $v) => $q->where('status', $v))
            ->when($request->query('channel'),     fn($q, $v) => $q->where('channel', $v))
            ->when($request->query('priority'),    fn($q, $v) => $q->where('priority', $v))
            ->when($request->query('assigned_to'), fn($q, $v) => $q->where('assigned_to', $v))
            ->orderBy('created_at', 'desc');

        // Non-admins only see leads assigned to them or unassigned
        if (!$user->hasRole('admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhereNull('assigned_to');
            });
        }

        return response()->json($query->get());
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
            'phone'              => 'nullable|string|max:20',
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
            'language'           => 'nullable|string|in:es,en',
            'assigned_to'        => 'nullable|exists:users,id',
        ]);

        $validated['phone'] = PhoneNormalizer::normalize($validated['phone'] ?? null);

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
            ['phone' => PhoneNormalizer::normalize($lead->phone)],
            [
                'name'     => $lead->name,
                'email'    => $lead->email,
                'company'  => $lead->company,
                'status'   => 'active',
                'language' => $lead->language ?? 'es',
            ]
        );

        $project = Project::create([
            'name'               => $lead->project_name ?? "Project: {$lead->name}",
            'category'           => $lead->category     ?? 'other',
            'client_id'          => $client->id,
            'stage'              => 'onboarding',
            'priority'           => $lead->priority     ?? 'medium',
            'commercial_summary' => $lead->commercial_summary,
            'channel'            => $lead->channel,
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
