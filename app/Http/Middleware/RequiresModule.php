<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequiresModule
{
    public function handle(Request $request, Closure $next, string $module): mixed
    {
        $org = $request->user()?->organization;

        if (!$org || !$org->hasModule($module)) {
            return response()->json([
                'error'   => 'module_not_available',
                'message' => 'This module is not included in your current plan.',
                'module'  => $module,
            ], 403);
        }

        return $next($request);
    }
}
