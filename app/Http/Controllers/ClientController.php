<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min($request->integer('per_page', 25), 100);

        return response()->json(
            Client::with(['projects', 'subscriptions'])
                ->when($request->status,   fn($q, $v) => $q->where('status', $v))
                ->when($request->language, fn($q, $v) => $q->where('language', $v))
                ->paginate($perPage)
        );
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

        if (array_key_exists('phone', $validated)) {
            $validated['phone'] = PhoneNormalizer::normalize($validated['phone']);
        }

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

        if (array_key_exists('phone', $validated)) {
            $validated['phone'] = PhoneNormalizer::normalize($validated['phone']);
        }

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

        $hasActiveProjects = $client->projects()
            ->whereNotIn('stage', ['closed_won', 'closed_lost'])
            ->exists();

        if ($hasActiveProjects) {
            return response()->json([
                'message' => 'This client has active projects. Close or delete them before continuing.',
            ], 422);
        }

        $client->delete();
        return response()->json(['message' => 'Client deleted successfully.']);
    }
}
