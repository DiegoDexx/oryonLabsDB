<?php

namespace App\Console\Commands\Metrics;

use App\Models\VoiceCallLog;
use App\Services\Metrics\VapiMetricsService;
use Illuminate\Console\Command;

class SyncVapiCalls extends Command
{
    protected $signature   = 'metrics:sync-vapi {--org= : Limit sync to a specific organization_id}';
    protected $description = 'Sync voice call data from Vapi into voice_call_logs';

    public function handle(VapiMetricsService $service): int
    {
        $orgId = $this->option('org') ? (int) $this->option('org') : null;

        $calls = $service->fetchCalls(['limit' => 100]);

        if (empty($calls)) {
            $this->warn('Vapi returned no calls. Check VAPI_API_KEY.');
            return self::FAILURE;
        }

        $synced = 0;

        foreach ($calls as $call) {
            $callId = $call['id'] ?? null;
            if (!$callId) continue;

            VoiceCallLog::updateOrCreate(
                ['vapi_call_id' => $callId],
                [
                    'organization_id'  => $orgId,
                    'duration_seconds' => (int) ($call['endedAt'] && $call['startedAt']
                        ? (strtotime($call['endedAt']) - strtotime($call['startedAt']))
                        : 0),
                    'cost_usd'         => $call['cost']             ?? 0,
                    'ended_reason'     => $call['endedReason']      ?? null,
                    'summary'          => $call['analysis']['summary']       ?? null,
                    'recording_url'    => $call['recordingUrl']     ?? null,
                ]
            );

            $synced++;
        }

        $this->info("Synced {$synced} Vapi calls.");

        return self::SUCCESS;
    }
}
