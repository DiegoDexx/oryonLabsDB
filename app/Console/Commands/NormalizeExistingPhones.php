<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Lead;
use App\Support\PhoneNormalizer;
use Illuminate\Console\Command;

class NormalizeExistingPhones extends Command
{
    protected $signature = 'phones:normalize';
    protected $description = 'Normaliza todos los teléfonos existentes a formato E.164';

    public function handle(): int
    {
        $leadCount = 0;
        Lead::whereNotNull('phone')->chunkById(100, function ($leads) use (&$leadCount) {
            foreach ($leads as $lead) {
                $normalized = PhoneNormalizer::normalize($lead->phone);
                if ($normalized && $normalized !== $lead->phone) {
                    $lead->phone = $normalized;
                    $lead->saveQuietly();
                    $leadCount++;
                }
            }
        });

        $clientCount = 0;
        Client::whereNotNull('phone')->chunkById(100, function ($clients) use (&$clientCount) {
            foreach ($clients as $client) {
                $normalized = PhoneNormalizer::normalize($client->phone);
                if ($normalized && $normalized !== $client->phone) {
                    $client->phone = $normalized;
                    $client->saveQuietly();
                    $clientCount++;
                }
            }
        });

        $this->info("Leads normalizados: {$leadCount}");
        $this->info("Clientes normalizados: {$clientCount}");
        return self::SUCCESS;
    }
}
