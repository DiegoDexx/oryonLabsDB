<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Activity;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(Project::with(['client', 'requirements'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'client_id' => 'required|exists:clients,id',
            'stage' => 'nullable|in:lead,contacted,proposal,negotiation,onboarding,active,closed_won,closed_lost',
            'priority' => 'nullable|in:low,medium,high',
            'estimated_delivery' => 'nullable|date',
        ]);

        $project = Project::create($validated);
        return response()->json($project->load('client'), 201);
    }

    public function show(Project $project)
    {
        return response()->json($project->load(['client', 'requirements.field', 'activities']));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string',
            'client_id' => 'sometimes|exists:clients,id',
            'stage' => 'nullable|in:lead,contacted,proposal,negotiation,onboarding,active,closed_won,closed_lost',
            'priority' => 'nullable|in:low,medium,high',
            'estimated_delivery' => 'nullable|date',
        ]);

        $project->update($validated);
        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json(null, 204);
    }

    public function indexFull()
    {
        $projects = Project::with(['client', 'requirements.field'])->get();

        $response = $projects->map(function ($project) {
            return [
                'client' => [
                    'id' => $project->client->id,
                    'name' => $project->client->name,
                    'email' => $project->client->email,
                ],
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'category' => $project->category,
                    'stage' => $project->stage,
                    'priority' => $project->priority,
                    'created_at' => $project->created_at,
                ],
                'requirements' => $project->requirements->map(function ($req) {
                    return [
                        'field_id' => $req->field->id,
                        'label' => $req->field->label,
                        'field_value' => $req->field_value,
                    ];
                }),
            ];
        });

        return response()->json($response);
    }

    public function showFull(Project $project)
    {
        $project->load(['client', 'requirements.field', 'activities']);

        $response = [
            'client' => [
                'id' => $project->client->id,
                'name' => $project->client->name,
                'email' => $project->client->email,
            ],
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'category' => $project->category,
                'stage' => $project->stage,
                'priority' => $project->priority,
                'created_at' => $project->created_at,
            ],
            'requirements' => $project->requirements->map(function ($req) {
                return [
                    'field_id' => $req->field->id,
                    'label' => $req->field->label,
                    'field_value' => $req->field_value,
                ];
            }),
        ];

        return response()->json([$response]);
    }

    public function updateStage(Request $request, Project $project)
    {
        $request->validate([
            'stage' => 'required|in:lead,contacted,proposal,negotiation,onboarding,active,closed_won,closed_lost'
        ]);

        $project->update(['stage' => $request->stage]);

        Activity::create([
            'client_id' => $project->client_id,
            'project_id' => $project->id,
            'type' => 'system',
            'description' => "Stage actualizado a: {$request->stage}"
        ]);

        return response()->json($project);
    }
}
