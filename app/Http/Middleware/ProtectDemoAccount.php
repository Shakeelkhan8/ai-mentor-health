<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * In demo mode every visitor shares one account. Applied to the routes that change
 * its credentials or profile (password, email/name/avatar, deletion), so one visitor
 * can't lock everyone else out. Visitors' own accounts are unaffected.
 */
class ProtectDemoAccount
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (config('mentor.demo.enabled') && $user !== null && $user->email === config('mentor.demo.email')) {
            return back()->with('error', "The shared demo account can't be changed. Sign up for your own account to try this.");
        }

        return $next($request);
    }
}
