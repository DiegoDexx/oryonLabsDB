<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectRequirement;

class ProjectController extends Controller
{
    //api crud
 public function indexFull()
{
    $projects = Project::with(['client', 'requirements.field'])->get();

    $response = $projects->map(function($project) {
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
                'created_at' => $project->created_at,
            ],
            'requirements' => $project->requirements->map(function($req) {
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

    public function show($id)
    {
        // Get a single project
    return response()->json(Project::find($id));
    }
public function store(Request $request)
{
    \Log::info('Payload recibido:', $request->all());

    $data = $request->all();

    if (is_string($data['requirements'] ?? null)) {
        $data['requirements'] = json_decode($data['requirements'], true);
    }

    $validated = validator($data, [
        'name' => 'required|string|max:255',
        'client_id' => 'required|exists:clients,id',
        'category' => 'required|string',
        'requirements' => 'array',
        'requirements.*.field_id' => 'required|exists:project_fields,id',
        'requirements.*.value' => 'required',
    ])->validate();

    \Log::info('Validated:', $validated);

    $project = Project::create([
        'name'      => $validated['name'],
        'client_id' => $validated['client_id'],
        'category'  => $validated['category'],
    ]);

    if (!empty($validated['requirements'])) {
        foreach ($validated['requirements'] as $req) {
            \Log::info('Guardando requirement:', $req);
            ProjectRequirement::create([
                'project_id' => $project->id,
                'field_id'   => $req['field_id'],
                'field_value'      => $req['value'],
            ]);
        }
    }

    return response()->json($project->load(['client', 'requirements.field']), 201);
}



    public function update(Request $request, $id)
    {
        // Update an existing project
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'client_id' => 'required|exists:clients,id',
        ]);

        $project = Project::find($id);
        $project->update($request->all());
        return response()->json($project);
    }

    public function destroy($id)
    {
        // Delete a project
        $project = Project::find($id);
        $project->delete();
        return response()->json(null, 204);
    }

    public function showFull(Project $project)
    {
        $project->load(['client', 'requirements.field']);

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

}

