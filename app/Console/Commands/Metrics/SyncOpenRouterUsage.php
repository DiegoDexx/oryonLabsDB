<?php

namespace App\Console\Commands\Metrics;

use App\Models\LlmUsageLog;
use App\Services\Metrics\OpenRouterMetricsService;
use Illuminate\Console\Command;

class SyncOpenRouterUsage extends Command
{
    protected $signature   = 'metrics:sync-openrouter {--org= : Limit sync to a specific organization_id}';
    protected $description = 'Sync LLM usage stats from OpenRouter into llm_usage_logs';

    public function handle(OpenRouterMetricsService $service): int
    {
        $activity = $service->fetchUserActivity();

        if (empty($activity)) {
            $this->warn('OpenRouter returned no activity data. Check OPENROUTER_MANAGEMENT_KEY.');
            return self::FAILURE;
        }

        // OpenRouter user-activity response doesn't contain per-generation data —
        // generations must be pushed individually via fetchGenerationStats(id) from webhook.
        // This command logs the summary for observability only.
        $this->info('OpenRouter activity fetched. Use generation webhook to populate per-row stats.');

        return self::SUCCESS;
    }

    public function syncGeneration(string $generationId, ?int $organizationId = null, ?int $leadId = null, string $channel = 'web'): void
    {
        $service = app(OpenRouterMetricsService::class);
        $data    = $service->fetchGenerationStats($generationId);

        if (empty($data)) {
            return;
        }

        LlmUsageLog::updateOrCreate(
            ['generation_id' => $generationId],
            [
                'organization_id'   => $organizationId,
                'lead_id'           => $leadId,
                'model'             => $data['model']              ?? 'unknown',
                'prompt_tokens'     => $data['tokens_prompt']      ?? 0,
                'completion_tokens' => $data['tokens_completion']  ?? 0,
                'cost_usd'          => $data['total_cost']         ?? 0,
                'channel'           => $channel,
            ]
        );
    }
}
