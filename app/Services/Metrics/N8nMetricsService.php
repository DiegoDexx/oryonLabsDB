<?php

namespace App\Services\Metrics;

use Illuminate\Support\Facades\Http;

class N8nMetricsService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.n8n.api_key', '');
        $this->baseUrl = rtrim(config('services.n8n.base_url', ''), '/');
    }

    public function fetchExecutions(array $params = []): array
    {
        $response = Http::withHeaders(['X-N8N-API-KEY' => $this->apiKey])
            ->get("{$this->baseUrl}/api/v1/executions", $params);

        if (!$response->successful()) {
            return [];
        }

        return $response->json('data', []);
    }
}
