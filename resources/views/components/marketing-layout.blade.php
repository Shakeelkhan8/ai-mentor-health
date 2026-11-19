@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head', ['title' => $title])
    </head>
    <body class="bg-white font-sans text-gray-900 antialiased">
        @php
            $links = [
                ['Features', route('home').'#features'],
                ['How it works', route('home').'#how-it-works'],
                ['Pricing', route('home').'#pricing'],
                ['FAQ', route('home').'#faq'],
                ['Contact', route('contact')],
            ];
        @endphp

        <header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-white/10 bg-ink/85 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-6">
                <x-brand dark href="{{ route('home') }}" />

                <nav class="hidden items-center gap-8 text-sm text-gray-300 md:flex">
                    @foreach ($links as [$label, $href])
                        <a href="{{ $href }}" class="transition hover:text-white">{{ $label }}</a>
                    @endforeach
                </nav>

                <div class="hidden items-center gap-3 md:flex">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">Open the app</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-300 transition hover:text-white">Log in</a>
                        <a href="{{ route('register') }}" class="btn-primary">Get started</a>
                    @endauth
                </div>

                <button type="button" @click="open = !open" class="rounded-lg p-2 text-gray-300 hover:bg-white/10 md:hidden" aria-label="Toggle menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>

            <div x-show="open" x-cloak class="border-t border-white/10 px-6 pb-6 pt-2 md:hidden">
                @foreach ($links as [$label, $href])
                    <a href="{{ $href }}" @click="open = false" class="block py-2 text-sm text-gray-300 hover:text-white">{{ $label }}</a>
                @endforeach
                <div class="mt-4 flex gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary w-full">Open the app</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary flex-1">Log in</a>
                        <a href="{{ route('register') }}" class="btn-primary flex-1">Get started</a>
                    @endauth
                </div>
            </div>
        </header>

        @if (session('success') || session('error'))
            <div class="mx-auto max-w-6xl px-6 pt-6">
                @if (session('success'))
                    <x-alert type="success">{{ session('success') }}</x-alert>
                @endif
                @if (session('error'))
                    <x-alert type="error">{{ session('error') }}</x-alert>
                @endif
            </div>
        @endif

        <main>
            {{ $slot }}
        </main>

        <footer class="bg-ink text-gray-400">
            <div class="mx-auto grid max-w-6xl gap-12 px-6 py-16 md:grid-cols-[1.4fr_1fr_1.4fr]">
                <div>
                    <x-brand dark href="{{ route('home') }}" />
                    <p class="mt-4 max-w-xs text-sm leading-relaxed">{{ config('mentor.tagline') }}</p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-white">Product</h3>
                    <ul class="mt-4 space-y-2 text-sm">
                        @foreach ($links as [$label, $href])
                            <li><a href="{{ $href }}" class="transition hover:text-white">{{ $label }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-white">Weekly wellness tips</h3>
                    <p class="mt-4 text-sm">One short, practical email. No spam.</p>
                    <form action="{{ route('sendmail') }}" method="POST" class="mt-4 flex gap-2">
                        @csrf
                        <label for="newsletter-email" class="sr-only">Email address</label>
                        <input id="newsletter-email" type="email" name="email" required placeholder="you@example.com"
                               class="min-w-0 flex-1 rounded-lg border-0 bg-white/5 px-3 py-2 text-sm text-white ring-1 ring-inset ring-white/10 placeholder:text-gray-500 focus:ring-2 focus:ring-brand-400">
                        <button type="submit" class="btn-primary">Subscribe</button>
                    </form>
                    {{-- The contact page has its own email field, so its errors stay there. --}}
                    @unless (request()->routeIs('contact'))
                        @error('email')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    @endunless
                </div>
            </div>

            <div class="border-t border-white/10">
                <div class="mx-auto flex max-w-6xl flex-col gap-2 px-6 py-6 text-xs text-gray-500 md:flex-row md:items-center md:justify-between">
                    <p>&copy; {{ date('Y') }} {{ config('mentor.name') }}</p>
                    <p class="max-w-xl md:text-right">{{ config('mentor.disclaimer') }}</p>
                </div>
            </div>
        </footer>
    </body>
</html>
