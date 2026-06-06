<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        return response()->json(Activity::with(['client', 'project'])->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'required|in:call,email,whatsapp,meeting,note,system',
            'description' => 'required|string',
        ]);

        $activity = Activity::create($validated);
        return response()->json($activity->load(['client', 'project']), 201);
    }

    public function show(Activity $activity)
    {
        return response()->json($activity->load(['client', 'project']));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'type' => 'sometimes|in:call,email,whatsapp,meeting,note,system',
            'description' => 'sometimes|string',
        ]);

        $activity->update($validated);
        return response()->json($activity);
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return response()->json(null, 204);
    }
}
