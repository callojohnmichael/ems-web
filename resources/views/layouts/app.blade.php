<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'Event Manager'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
         @stack('styles')
    </head>

    <body class="min-h-screen bg-gray-50 font-sans antialiased">
        @auth
            @include('layouts.partials.sidebar')
            @stack('scripts')

            <div class="pl-64">
                <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/95 backdrop-blur">
                    <div class="flex h-16 items-center justify-between px-6">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
                            <p class="text-sm text-gray-500">Here's what's happening with your school events today.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200 text-gray-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
                            <span class="rounded-full bg-gradient-to-r from-violet-600 to-purple-600 px-3 py-0.5 text-xs font-medium text-white">
                                {{ ucfirst(str_replace('_', ' ', Auth::user()->getRoleNames()->first() ?? 'user')) }}
                            </span>
                        </div>
                    </div>
                </header>

                <main class="p-6">
                    @if(session('status'))
                        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- The $slot variable captures content from <x-app-layout> --}}
                    {{ $slot }}

                    @if(session('success'))
                        <div class="fixed bottom-6 right-6 z-50 max-w-sm rounded-lg border border-green-200 bg-green-400 p-4 shadow-lg" role="alert">
                            <p class="font-semibold text-green-900">Success!</p>
                            <p class="mt-1 text-sm text-gray-600">{{ session('success') }}</p>
                        </div>
                    @endif
                </main>
            </div>
        @else
            <div class="min-h-screen bg-gray-100">
                @include('layouts.navigation')
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">{{ $header }}</div>
                    </header>
                @endisset
                <main>
                    {{ $slot }}
                </main>
            </div>
        @endauth
    </body>
</html>