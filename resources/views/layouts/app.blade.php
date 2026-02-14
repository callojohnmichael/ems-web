<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'Event Manager'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
                        <div class="relative" x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-gray-100"
                            >
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200 text-gray-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
                                <span class="rounded-full bg-gradient-to-r from-violet-600 to-purple-600 px-3 py-0.5 text-xs font-medium text-white">
                                    {{ ucfirst(str_replace('_', ' ', Auth::user()->getRoleNames()->first() ?? 'user')) }}
                                </span>
                                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                @click.outside="open = false"
                                x-transition
                                class="absolute right-0 z-40 mt-2 w-48 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-lg"
                                style="display: none;"
                            >
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-800 hover:bg-gray-50">
                                    <i class="bi bi-person text-xl leading-none text-gray-900"></i>
                                    Profile
                                </a>

                                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 px-4 py-2.5 text-left text-sm text-red-600 hover:bg-red-50">
                                        <i class="bi bi-box-arrow-right text-base leading-none"></i>
                                        Log out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- ✅ THIS IS THE MISSING PART --}}
                @isset($header)
                    <div class="border-b border-gray-200 bg-white">
                        <div class="px-6 py-5">
                            {{ $header }}
                        </div>
                    </div>
                @endisset

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
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        @endauth
    </body>
</html>
