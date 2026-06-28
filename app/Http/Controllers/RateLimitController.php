<?php

namespace App\Http\Controllers;

use App\Models\RateLimitOffense;
use App\Support\PhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RateLimitController extends Controller
{
    public function checkRateLimit(Request $request): JsonResponse
    {
        $phone = PhoneNormalizer::normalize($request->query('phone', ''));

        if (!$phone) {
            return response()->json(['blocked' => false], 200);
        }

        $offense = RateLimitOffense::firstOrCreate(['phone' => $phone]);

        if ($offense->blocked_until && $offense->blocked_until->isFuture()) {
            return response()->json([
                'blocked'         => true,
                'reason'          => 'escalated_block',
                'blocked_until'   => $offense->blocked_until,
                'violation_count' => $offense->violation_count,
            ], 200);
        }

        // Reset counter after 30 clean days
        if ($offense->last_violation_at && $offense->last_violation_at->lt(now()->subDays(30))) {
            $offense->violation_count = 0;
            $offense->save();
        }

        $key   = "msg_count:{$phone}";
        $count = Cache::get($key, 0) + 1;
        Cache::put($key, $count, now()->addMinutes(10));

        if ($count > 15) {
            $offense->violation_count    += 1;
            $offense->last_violation_at   = now();
            $offense->blocked_until       = match (true) {
                $offense->violation_count === 1 => now()->addMinutes(10),
                $offense->violation_count === 2 => now()->addHours(2),
                default                         => now()->addHours(24),
            };
            $offense->save();
            Cache::forget($key);

            return response()->json([
                'blocked'         => true,
                'reason'          => 'rate_limit_exceeded',
                'blocked_until'   => $offense->blocked_until,
                'violation_count' => $offense->violation_count,
            ], 200);
        }

        return response()->json(['blocked' => false, 'count' => $count], 200);
    }
}
