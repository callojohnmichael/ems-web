<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Event Manager') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f5f3ff] text-slate-900">
        <div class="min-h-screen">
            <header class="px-6 pt-8">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-[#5b6cf7] to-[#b073f2] shadow-lg shadow-[#7a74f5]/30">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M12 3L2.5 7.5L12 12L21.5 7.5L12 3Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path d="M4 10.5V16.5L12 21L20 16.5V10.5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path d="M8.5 13V17.25" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">Event Manager</p>
                            <p class="text-xs text-slate-500">School Event Management System</p>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <div class="hidden items-center gap-3 text-sm font-medium text-slate-600 sm:flex">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-slate-600 transition hover:text-slate-900">Log in</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-full bg-gradient-to-r from-[#5b6cf7] to-[#b073f2] px-4 py-2 text-white shadow-lg shadow-[#7a74f5]/30 transition hover:-translate-y-0.5">
                                        Sign up
                                    </a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>
            </header>

            <main class="px-6 pb-20 pt-10">
                <section class="mx-auto w-full max-w-6xl">
                    <div class="relative overflow-hidden rounded-[32px] border border-white/70 bg-gradient-to-br from-[#dfe3ff] via-[#d9d4f7] to-[#d4d9ff] px-6 py-16 shadow-2xl shadow-[#9aa4ff]/25 sm:px-12">
                        <div class="pointer-events-none absolute inset-0 opacity-35" style="background-image: radial-gradient(circle at 20% 20%, #ffffff 0%, transparent 55%), radial-gradient(circle at 80% 20%, #ffffff 0%, transparent 50%), radial-gradient(circle at 50% 80%, #ffffff 0%, transparent 60%);"></div>
                        <div class="pointer-events-none absolute -right-24 top-10 h-56 w-56 rounded-full bg-[#7b6cf4]/30 blur-3xl"></div>
                        <div class="pointer-events-none absolute -left-20 bottom-0 h-56 w-56 rounded-full bg-[#b073f2]/30 blur-3xl"></div>

                        <div class="relative flex flex-col items-center text-center">
                            <h1 class="text-3xl font-semibold text-slate-900 sm:text-5xl">
                                School Event
                                <span class="block text-[#6f62f7]">Management System</span>
                            </h1>
                            <p class="mt-4 max-w-2xl text-sm text-slate-600 sm:text-base">
                                Streamline your school events from planning to execution. Manage participants, approvals, and analytics all in one beautiful platform.
                            </p>
                            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
                                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#5b6cf7] to-[#b073f2] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-[#7a74f5]/30 transition hover:-translate-y-0.5">
                                    Get Started
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M5 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                                <a href="#features" class="inline-flex items-center justify-center rounded-full border border-white/70 bg-white/80 px-6 py-3 text-sm font-semibold text-slate-700 shadow transition hover:-translate-y-0.5">
                                    Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="features" class="mx-auto mt-20 w-full max-w-6xl text-center">
                    <h2 class="text-2xl font-semibold text-slate-900 sm:text-3xl">Everything You Need to Manage Events</h2>
                    <p class="mt-3 text-sm text-slate-500 sm:text-base">
                        From event creation to post-event analytics, we've got you covered with powerful tools and intuitive design.
                    </p>

                    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="rounded-2xl border border-slate-100 bg-white p-8 text-left shadow-xl shadow-slate-200/60">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#6f62f7] text-white shadow-lg shadow-[#7a74f5]/30">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <rect x="4" y="5" width="16" height="15" rx="2" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M8 3V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M16 3V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M4 10H20" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-slate-800">Event Planning</h3>
                            <p class="mt-2 text-sm text-slate-500">Create and manage events with detailed scheduling, venue booking, and comprehensive planning tools.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-8 text-left shadow-xl shadow-slate-200/60">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#6f62f7] text-white shadow-lg shadow-[#7a74f5]/30">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M16 11C17.6569 11 19 9.65685 19 8C19 6.34315 17.6569 5 16 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M8 11C6.34315 11 5 9.65685 5 8C5 6.34315 6.34315 5 8 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M12 13C14.2091 13 16 11.2091 16 9C16 6.79086 14.2091 5 12 5C9.79086 5 8 6.79086 8 9C8 11.2091 9.79086 13 12 13Z" stroke="currentColor" stroke-width="1.5" />
                                    <path d="M5 20C5.8 17.6 7.9 16 12 16C16.1 16 18.2 17.6 19 20" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-slate-800">Participant Management</h3>
                            <p class="mt-2 text-sm text-slate-500">Handle registrations, track attendance, and manage participant data with ease and efficiency.</p>
                        </div>
                        <div class="rounded-2xl border border-slate-100 bg-white p-8 text-left shadow-xl shadow-slate-200/60">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#6f62f7] text-white shadow-lg shadow-[#7a74f5]/30">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M7 12L10 15L17 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" />
                                </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-slate-800">Approval Workflow</h3>
                            <p class="mt-2 text-sm text-slate-500">Streamlined approval process with automated notifications and role-based access control.</p>
                        </div>
                    </div>
                </section>

                <section class="mx-auto mt-16 w-full max-w-6xl">
                    <div class="rounded-[28px] border border-slate-100 bg-white px-8 py-14 text-center shadow-2xl shadow-slate-200/60">
                        <h3 class="text-2xl font-semibold text-slate-900">Ready to Transform Your Event Management?</h3>
                        <p class="mt-3 text-sm text-slate-500 sm:text-base">Join thousands of schools already using our platform to create memorable events.</p>
                        <div class="mt-8 flex justify-center">
                            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-[#5b6cf7] to-[#b073f2] px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-[#7a74f5]/30 transition hover:-translate-y-0.5">
                                Start Your Journey
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                    <path d="M5 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    <path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-100 bg-white/80 py-8 text-center text-sm text-slate-500">
                &copy; {{ date('Y') }} School Event Management System.
            </footer>
        </div>
    </body>
</html>
