<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProjectRequirement;

class ProjectRequirementController extends Controller
{
    //api 
    public function index(Request $request)
    {
        $query = ProjectRequirement::query();

        if ($request->has('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'field_id'   => 'required|exists:project_fields,id',
            'field_value'=> 'required|string',
        ]);

        $requirement = ProjectRequirement::create($validated);

        return response()->json($requirement, 201);
    }

    public function update(Request $request, ProjectRequirement $projectRequirement)
    {
        $validated = $request->validate([
            'field_value'=> 'sometimes|required|string',
        ]);

        $projectRequirement->update($validated);

        return response()->json($projectRequirement);
    }

    public function destroy(ProjectRequirement $projectRequirement)
    {
        $projectRequirement->delete();
        return response()->json(['message' => 'Requisito eliminado correctamente']);
    }
}
