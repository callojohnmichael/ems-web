<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approvals / Rejections') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="font-medium">Admin-only page.</p>
                    <p class="mt-2 text-sm text-gray-600">If you see this, you have the admin role. Event approval/rejection UI will be built in a later phase.</p>
                    <p class="mt-4"><a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:underline">Back to Admin dashboard</a></p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
