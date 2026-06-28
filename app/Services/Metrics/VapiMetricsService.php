<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Http;

class VapiMetricsService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.vapi.ai';

    public function __construct()
    {
        $this->apiKey = config('services.vapi.api_key', '');
    }

    public function fetchCalls(array $params = []): array
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/call", $params);

        if (!$response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }

    public function fetchAnalytics(array $queries): array
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/analytics", ['queries' => $queries]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json() ?? [];
    }
}
