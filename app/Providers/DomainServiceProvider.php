<?php

namespace App\Providers;

use App\Domains\Billing\Contracts\BillingGateway;
use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Infrastructure\Ai\DemoCoachClient;
use App\Infrastructure\Ai\RapidApiCoachClient;
use App\Infrastructure\Billing\StripeBillingGateway;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register domain ↔ infrastructure bindings.
     */
    public function register(): void
    {
        // Resolved lazily so DEMO_MODE is read when the client is needed, not at boot.
        $this->app->bind(AiCoachClient::class, fn ($app) => config('mentor.demo.enabled')
            ? $app->make(DemoCoachClient::class)
            : $app->make(RapidApiCoachClient::class));
        $this->app->bind(BillingGateway::class, StripeBillingGateway::class);
    }

    public function boot(): void
    {
        //
    }
}
