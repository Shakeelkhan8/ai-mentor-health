@php
    $user = Auth::user();
    $onboarded = $user->profile?->hasCompletedOnboarding() ?? false;

    // Only modules the user can open: the routes behind a flag or onboarding would 404 or redirect.
    $links = array_filter([
        $onboarded ? ['dashboard', 'Dashboard', 'dashboard'] : ['onboarding.show', 'Get started', 'onboarding.*'],
        $onboarded && config('mentor.features.ai_coach') ? ['coach.index', 'AI Coach', 'coach.*'] : null,
        $onboarded && config('mentor.features.weekly_plans') ? ['plans.weekly.show', 'Weekly plan', 'plans.*'] : null,
        $onboarded && config('mentor.features.subscriptions') ? ['billing.show', 'Billing', 'billing.*'] : null,
        $onboarded && $user->can('admin') ? ['community-forms', 'Contact messages', 'community-forms'] : null,
    ]);
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-40 border-b border-gray-200/70 bg-white/90 backdrop-blur">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Brand -->
                <div class="shrink-0 flex items-center">
                    <x-brand :href="$onboarded ? route('dashboard') : route('onboarding.show')" />
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @foreach ($links as [$route, $label, $pattern])
                        <x-nav-link :href="route($route)" :active="request()->routeIs($pattern)">
                            {{ $label }}
                        </x-nav-link>
                    @endforeach
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ $user->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if ($onboarded)
                            <x-dropdown-link :href="route('edit_profile')">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                        @endif

                        <!-- Logout is a POST form with a real button: a GET link to it would be a CSRF hole. -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out" aria-label="Menu">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @foreach ($links as [$route, $label, $pattern])
                <x-responsive-nav-link :href="route($route)" :active="request()->routeIs($pattern)">
                    {{ $label }}
                </x-responsive-nav-link>
            @endforeach
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ $user->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ $user->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                @if ($onboarded)
                    <x-responsive-nav-link :href="route('edit_profile')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
