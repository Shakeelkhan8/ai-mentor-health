<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Hiding a menu link is not access control: admin-only routes use `can:admin`.
        // Registration always assigns role "user", so this cannot be self-granted.
        Gate::define('admin', fn (User $user) => $user->role === 'admin');
    }
}
