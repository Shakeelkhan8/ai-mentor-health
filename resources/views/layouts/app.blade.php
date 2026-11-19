<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="border-b border-gray-200/70 bg-white">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @if (session('success') || session('error'))
                    <div class="max-w-7xl mx-auto pt-6 px-4 sm:px-6 lg:px-8">
                        @if (session('success'))
                            <x-alert type="success">{{ session('success') }}</x-alert>
                        @endif
                        @if (session('error'))
                            <x-alert type="error">{{ session('error') }}</x-alert>
                        @endif
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
