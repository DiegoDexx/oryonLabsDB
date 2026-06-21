<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 25), 100);

        return response()->json(
            Activity::with(['client', 'project'])
                ->when($request->query('client_id'),  fn($q, $v) => $q->where('client_id', $v))
                ->when($request->query('project_id'), fn($q, $v) => $q->where('project_id', $v))
                ->when($request->query('type'),       fn($q, $v) => $q->where('type', $v))
                ->latest()
                ->paginate($perPage)
        );
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
