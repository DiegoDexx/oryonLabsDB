<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        return response()->json(Subscription::with('client')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'plan' => 'required|in:starter,pro,professional,voice_ai',
            'setup_fee' => 'required|numeric|min:0',
            'monthly_fee' => 'required|numeric|min:0',
            'status' => 'nullable|in:active,paused,cancelled,pending',
            'start_date' => 'required|date',
            'next_billing_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $subscription = Subscription::create($validated);
        return response()->json($subscription->load('client'), 201);
    }

    public function show(Subscription $subscription)
    {
        return response()->json($subscription->load(['client', 'invoices']));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'client_id' => 'sometimes|exists:clients,id',
            'plan' => 'sometimes|in:starter,pro,professional,voice_ai',
            'setup_fee' => 'sometimes|numeric|min:0',
            'monthly_fee' => 'sometimes|numeric|min:0',
            'status' => 'nullable|in:active,paused,cancelled,pending',
            'start_date' => 'sometimes|date',
            'next_billing_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $subscription->update($validated);
        return response()->json($subscription);
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, Subscription $subscription)
    {
        $request->validate(['status' => 'required|in:active,paused,cancelled,pending']);
        $subscription->update(['status' => $request->status]);
        return response()->json($subscription);
    }
}
