<?php

namespace App\Providers;

use App\Domains\Billing\Contracts\BillingGateway;
use App\Domains\Coaching\Contracts\AiCoachClient;
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
        $this->app->bind(AiCoachClient::class, RapidApiCoachClient::class);
        $this->app->bind(BillingGateway::class, StripeBillingGateway::class);
    }

    public function boot(): void
    {
        //
    }
}
