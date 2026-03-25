<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyFaxApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $configuredToken = (string) config('services.fax_api.token', '');
        if ($configuredToken === '') {
            return response()->json([
                'success' => false,
                'error' => 'FAX API token is not configured',
            ], 503);
        }

        $authHeader = (string) $request->header('Authorization', '');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 401);
        }

        $incomingToken = substr($authHeader, 7);
        if (!hash_equals($configuredToken, $incomingToken)) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
