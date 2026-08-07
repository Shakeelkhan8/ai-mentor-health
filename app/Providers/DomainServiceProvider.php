<?php

namespace App\Providers;

use App\Domains\Coaching\Contracts\AiCoachClient;
use App\Infrastructure\Ai\RapidApiCoachClient;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register domain ↔ infrastructure bindings.
     */
    public function register(): void
    {
        $this->app->bind(AiCoachClient::class, RapidApiCoachClient::class);
    }

    public function boot(): void
    {
        //
    }
}
