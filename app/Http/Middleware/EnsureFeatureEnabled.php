<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFeatureEnabled
{
    /**
     * Ensure a named feature flag from config/mentor.php is enabled.
     *
     * Usage: middleware('feature:care_marketplace')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $features = config('mentor.features', []);

        if (! array_key_exists($feature, $features)) {
            abort(404);
        }

        if (! $features[$feature]) {
            abort(404);
        }

        return $next($request);
    }
}
