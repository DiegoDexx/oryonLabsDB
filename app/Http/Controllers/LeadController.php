<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Activity;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre_apellidos' => 'required|string',
            'correo' => 'nullable|email',
            'telefono' => 'required|string',
            'servicio' => 'required|string',
            'ciudad' => 'nullable|string',
            'prioridad' => 'nullable|in:low,medium,high',
            'presupuesto' => 'nullable|string',
            'fecha_inicio' => 'nullable|string',
            'resumen_comercial' => 'nullable|string',
            'canal' => 'nullable|in:web,whatsapp,formulario',
        ]);

        // Crear o actualizar cliente
        $client = Client::updateOrCreate(
            ['phone' => $validated['telefono']],
            [
                'name' => $validated['nombre_apellidos'],
                'email' => $validated['correo'] ?? null,
            ]
        );

        // Crear proyecto
        $project = Project::create([
            'name' => "Lead: {$validated['servicio']}",
            'category' => 'automatizacion_ia',
            'client_id' => $client->id,
            'stage' => 'lead',
            'priority' => $validated['prioridad'] ?? 'medium',
        ]);

        // Registrar actividad
        Activity::create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'type' => 'system',
            'description' => "Lead captado via {$validated['canal']}. {$validated['resumen_comercial']}"
        ]);

        return response()->json([
            'client' => $client,
            'project' => $project,
        ], 201);
    }
}
