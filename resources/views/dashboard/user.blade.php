<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="font-medium">You are logged in as <strong>User</strong>.</p>
                    <p class="mt-2 text-sm text-gray-600">This page is only visible to users with the user role.</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Role test links (User only)</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li><a href="{{ route('user.requests') }}" class="text-indigo-600 hover:underline">My event requests</a> — user-only page</li>
                        <li><a href="{{ route('user.dashboard') }}" class="text-indigo-600 hover:underline">User dashboard</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
