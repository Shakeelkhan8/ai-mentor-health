<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted proxies
    |--------------------------------------------------------------------------
    | Hosts like Render terminate HTTPS at their load balancer and forward plain
    | HTTP with X-Forwarded-* headers. Trusting that proxy makes Laravel generate
    | https:// URLs and see the real client IP (which the rate limiters key on).
    |
    | Set TRUSTED_PROXIES to "*" only where the app is reachable solely through
    | the host's proxy, or to a comma-separated list of proxy IPs. Unset or empty
    | (as in .env.example), forwarded headers are ignored, so a client cannot spoof
    | its IP or scheme. App\Http\Middleware\TrustProxies falls back to this value.
    */
    'proxies' => env('TRUSTED_PROXIES') ?: null,

];
