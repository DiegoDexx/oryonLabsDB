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
            'nombre'            => 'required|string|max:255',
            'empresa'           => 'nullable|string|max:255',
            'email'             => 'nullable|email|max:255',
            'telefono'          => 'required|string|max:20',
            'canal'             => 'nullable|string|max:50',
            'reto'              => 'nullable|string',
            'volumen_clientes'  => 'nullable|string|max:50',
            'herramientas'      => 'nullable|string',
            'urgencia'          => 'nullable|string|max:100',
            'categoria'         => 'nullable|string|max:100',
            'project_name'      => 'nullable|string|max:255',
            'prioridad'         => 'nullable|in:low,medium,high',
            'plan_sugerido'     => 'nullable|string|max:50',
            'resumen_comercial' => 'nullable|string',
        ]);

        $lead = Lead::create([
            ...$validated,
            'status'    => 'nuevo',
            'canal'     => $validated['canal']     ?? 'formulario',
            'prioridad' => $validated['prioridad'] ?? 'medium',
        ]);

        return response()->json([
            'success' => true,
            'lead'    => $lead,
        ], 201);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $request->validate([
            'status' => 'required|in:nuevo,contactado,calificado,convertido,descartado',
        ]);
        $lead->update(['status' => $request->status]);
        return response()->json($lead);
    }

    public function updateNotes(Request $request, Lead $lead)
    {
        $request->validate(['notas' => 'nullable|string']);
        $lead->update(['notas' => $request->notas]);
        return response()->json($lead);
    }

    public function convert(Request $request, Lead $lead)
    {
        $client = Client::updateOrCreate(
            ['phone' => $lead->telefono],
            [
                'name'    => $lead->nombre,
                'email'   => $lead->email,
                'company' => $lead->empresa,
                'status'  => 'active',
            ]
        );

        $project = Project::create([
            'name'              => $lead->project_name ?? "Proyecto: {$lead->nombre}",
            'category'          => $lead->categoria    ?? 'otros',
            'client_id'         => $client->id,
            'stage'             => 'onboarding',
            'priority'          => $lead->prioridad    ?? 'medium',
            'resumen_comercial' => $lead->resumen_comercial,
            'canal'             => $lead->canal,
        ]);

        Activity::create([
            'client_id'   => $client->id,
            'project_id'  => $project->id,
            'type'        => 'system',
            'description' => "Lead convertido a cliente desde canal: {$lead->canal}",
        ]);

        $lead->update([
            'status'     => 'convertido',
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
