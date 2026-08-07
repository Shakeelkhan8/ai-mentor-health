<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Redirect authenticated users to onboarding until their profile is complete.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($request->routeIs('onboarding.*', 'logout', 'verification.*')) {
            return $next($request);
        }

        $completed = $user->profile?->hasCompletedOnboarding() ?? false;

        if (! $completed) {
            return redirect()->route('onboarding.show');
        }

        return $next($request);
    }
}
