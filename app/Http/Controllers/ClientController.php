<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        return response()->json(Client::with(['projects', 'subscriptions'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'company'  => 'nullable|string|max:255',
            'email'    => 'nullable|string|email|max:255|unique:clients',
            'phone'    => 'nullable|string|max:20',
            'status'   => 'nullable|in:active,inactive,churned',
            'language' => 'nullable|string|in:es,en',
        ]);

        $client = Client::create([
            ...$validated,
            'language' => $validated['language'] ?? 'es',
        ]);
        return response()->json($client, 201);
    }

    public function show(Client $client)
    {
        return response()->json($client->load(['projects', 'subscriptions', 'activities', 'invoices']));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'company'  => 'nullable|string|max:255',
            'email'    => 'sometimes|string|email|max:255|unique:clients,email,' . $client->id,
            'phone'    => 'nullable|string|max:20',
            'status'   => 'nullable|in:active,inactive,churned',
            'language' => 'nullable|string|in:es,en',
        ]);

        $client->update($validated);
        return response()->json($client);
    }

    public function destroy(Client $client)
    {
        $activeSubscription = $client->subscriptions()
            ->where('status', 'active')
            ->exists();

        if ($activeSubscription) {
            return response()->json([
                'message' => 'This client has an active subscription. Cancel it before deleting.',
            ], 422);
        }

        $client->delete(); // soft delete — sets deleted_at
        return response()->json(['message' => 'Client deleted successfully.']);
    }
}
