<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="grid min-h-screen lg:grid-cols-2">
            {{-- Brand panel: shown beside the form on large screens. --}}
            <aside class="relative hidden overflow-hidden bg-ink lg:flex lg:flex-col lg:justify-between lg:p-12">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(60rem_40rem_at_10%_-10%,rgba(16,185,133,0.22),transparent_60%)]"></div>
                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.04)_1px,transparent_1px)] bg-[size:48px_48px]"></div>

                <x-brand dark href="{{ route('home') }}" class="relative" />

                <div class="relative max-w-md">
                    <h2 class="text-3xl font-semibold leading-tight tracking-tight text-white">
                        A wellness coach that remembers your goals.
                    </h2>
                    <ul class="mt-8 space-y-4 text-sm text-gray-300">
                        <li class="flex gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-400"></span>Chat with an AI coach about sleep, stress, nutrition and fitness.</li>
                        <li class="flex gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-400"></span>Check in on daily habits and watch your streaks grow.</li>
                        <li class="flex gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-brand-400"></span>Get a weekly plan built from your goals and recent check-ins.</li>
                    </ul>
                </div>

                <p class="relative max-w-md text-xs leading-relaxed text-gray-500">{{ config('mentor.disclaimer') }}</p>
            </aside>

            {{-- Form panel --}}
            <main class="flex flex-col items-center justify-center bg-gray-50 px-6 py-12">
                <x-brand href="{{ route('home') }}" class="mb-8 lg:hidden" />

                <div class="w-full max-w-md rounded-2xl border border-gray-200/70 bg-white p-8 shadow-sm">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
