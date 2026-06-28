<?php

namespace App\Console\Commands\Metrics;

use App\Models\WorkflowExecutionLog;
use App\Services\Metrics\N8nMetricsService;
use Illuminate\Console\Command;

class SyncN8nExecutions extends Command
{
    protected $signature   = 'metrics:sync-n8n {--org= : Limit sync to a specific organization_id}';
    protected $description = 'Sync workflow executions from n8n into workflow_execution_logs';

    public function handle(N8nMetricsService $service): int
    {
        $orgId      = $this->option('org') ? (int) $this->option('org') : null;
        $executions = $service->fetchExecutions(['limit' => 100]);

        if (empty($executions)) {
            $this->warn('n8n returned no executions. Check N8N_API_KEY and N8N_BASE_URL.');
            return self::FAILURE;
        }

        $synced = 0;

        foreach ($executions as $execution) {
            $execId = (string) ($execution['id'] ?? null);
            if (!$execId) continue;

            WorkflowExecutionLog::updateOrCreate(
                ['n8n_execution_id' => $execId],
                [
                    'organization_id' => $orgId,
                    'workflow_name'   => $execution['workflowData']['name'] ?? 'unknown',
                    'status'          => $execution['status']               ?? 'unknown',
                    'duration_ms'     => isset($execution['startedAt'], $execution['stoppedAt'])
                        ? (int) ((strtotime($execution['stoppedAt']) - strtotime($execution['startedAt'])) * 1000)
                        : null,
                ]
            );

            $synced++;
        }

        $this->info("Synced {$synced} n8n executions.");

        return self::SUCCESS;
    }
}
