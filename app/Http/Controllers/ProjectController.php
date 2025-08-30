<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

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
                    'field_name' => $req->field->field_name,
                    'label' => $req->field->label,
                    'value' => $req->field_value,
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
        // Create a new project
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'client_id' => 'required|exists:clients,id',
        ]);

        $project = Project::create($request->all());
        return response()->json($project, 201);
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
    $project->load(['client', 'requirements.field']); // carga cliente y cada field

    $response = [
        'client' => [
            'name' => $project->client->name,
            'email' => $project->client->email,
        ],
        'project' => [
            'name' => $project->name,
            'category' => $project->category,
        ],
        'requirements' => $project->requirements->map(function($req) {
            return [
                'field_name' => $req->field->field_name,
                'label' => $req->field->label,
                'value' => $req->field_value,
            ];
        }),
    ];

    return response()->json($response);
}
}

