<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <title>{{ config('app.name', 'Event Manager') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gradient-to-b from-[#f5f7ff] via-[#f8f7ff] to-[#f3f1ff] text-slate-800">
        <div class="relative min-h-screen overflow-hidden">
            <div class="pointer-events-none absolute -top-32 right-0 h-96 w-96 rounded-full bg-[#6f7bf7]/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-40 left-10 h-[28rem] w-[28rem] rounded-full bg-[#9b6df3]/20 blur-3xl"></div>

            <div class="relative flex min-h-screen flex-col items-center px-6 pb-12 pt-12 sm:pt-16">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-3">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-[#5b6cf7] to-[#b073f2] shadow-lg shadow-[#7a74f5]/30">
                        <svg class="h-8 w-8 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 3L2.5 7.5L12 12L21.5 7.5L12 3Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                            <path d="M4 10.5V16.5L12 21L20 16.5V10.5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                            <path d="M8.5 13V17.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-semibold tracking-tight text-slate-900">{{ config('app.name', 'Event Manager') }}</h1>
                    <p class="text-sm text-slate-500">School Event Management System</p>
                </a>

                <div class="mt-10 w-full max-w-md">
                    <div class="rounded-2xl border border-white/60 bg-white/80 p-8 shadow-xl shadow-slate-200/70 backdrop-blur">
                        <div class="text-center">
                            <h2 class="text-xl font-semibold text-[#6f62f7]">Welcome Back</h2>
                            <p class="mt-1 text-sm text-slate-500">Sign in to manage your school events</p>
                        </div>

                        <div class="mt-6 rounded-full bg-slate-100 p-1">
                            <div class="grid grid-cols-2 text-center text-sm font-medium">
                                <span class="rounded-full bg-white px-3 py-2 text-slate-800 shadow">Login</span>
                                @if (Route::has('register'))
                                    <a class="rounded-full px-3 py-2 text-slate-500 transition hover:text-slate-800" href="{{ route('register') }}">Sign Up</a>
                                @else
                                    <span class="rounded-full px-3 py-2 text-slate-400">Sign Up</span>
                                @endif
                            </div>
                        </div>

                        <form class="mt-6 space-y-5" method="POST" action="{{ route('login') }}">
                            @csrf

                            <div>
                                <label for="email" class="text-sm font-medium text-slate-700">Email</label>
                                <div class="mt-2">
                                    <input
                                        id="email"
                                        name="email"
                                        type="email"
                                        autocomplete="username"
                                        required
                                        autofocus
                                        value="{{ old('email') }}"
                                        placeholder="Enter your email"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#7b6cf4] focus:ring-2 focus:ring-[#7b6cf4]/20"
                                    />
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="text-sm font-medium text-slate-700">Password</label>
                                <div class="relative mt-2">
                                    <input
                                        id="password"
                                        name="password"
                                        type="password"
                                        autocomplete="current-password"
                                        required
                                        placeholder="Enter your password"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-12 text-sm text-slate-700 shadow-sm outline-none transition focus:border-[#7b6cf4] focus:ring-2 focus:ring-[#7b6cf4]/20"
                                    />
                                    <button
                                        type="button"
                                        id="toggle-password"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full p-1 text-slate-400 transition hover:text-slate-600"
                                        aria-label="Toggle password visibility"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path d="M2.25 12C4.5 7.5 8.25 5.25 12 5.25C15.75 5.25 19.5 7.5 21.75 12C19.5 16.5 15.75 18.75 12 18.75C8.25 18.75 4.5 16.5 2.25 12Z" stroke="currentColor" stroke-width="1.5" />
                                            <circle cx="12" cy="12" r="3.25" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                                <label for="remember_me" class="flex items-center gap-2 text-slate-600">
                                    <input
                                        id="remember_me"
                                        name="remember"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-slate-300 text-[#6f62f7] focus:ring-[#6f62f7]/30"
                                    />
                                    Remember me
                                </label>

                                @if (Route::has('password.request'))
                                    <a class="text-slate-500 transition hover:text-slate-700" href="{{ route('password.request') }}">
                                        Forgot your password?
                                    </a>
                                @endif
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-xl bg-gradient-to-r from-[#5b6cf7] to-[#b073f2] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-[#7a74f5]/30 transition hover:-translate-y-0.5 hover:shadow-[#7a74f5]/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#7b6cf4]/50"
                            >
                                Login
                            </button>
                        </form>
                    </div>
                </div>

                <p class="mt-8 text-sm text-slate-500">&copy; {{ date('Y') }} School Event Management System</p>
            </div>
        </div>

        <script>
            const togglePassword = document.getElementById('toggle-password');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', () => {
                    const isHidden = passwordInput.type === 'password';
                    passwordInput.type = isHidden ? 'text' : 'password';
                });
            }
        </script>
    </body>
</html>
