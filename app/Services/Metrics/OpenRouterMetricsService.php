<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Http;

class OpenRouterMetricsService
{
    private string $apiKey;
    private string $managementKey;

    public function __construct()
    {
        $this->apiKey        = config('services.openrouter.api_key', '');
        $this->managementKey = config('services.openrouter.management_key', '');
    }

    public function fetchGenerationStats(string $generationId): array
    {
        $response = Http::withToken($this->apiKey)
            ->get('https://openrouter.ai/api/v1/generation', ['id' => $generationId]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json('data', []);
    }

    public function fetchUserActivity(): array
    {
        $response = Http::withToken($this->managementKey)
            ->get('https://openrouter.ai/api/v1/auth/key');

        if (!$response->successful()) {
            return [];
        }

        return $response->json('data', []);
    }
}
