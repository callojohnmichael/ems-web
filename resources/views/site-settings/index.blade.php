<x-app-layout :hide-sidebar="true">
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Site Settings</h2>
                <p class="mt-1 text-sm text-gray-500">Secret menu access control by role.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Back To Dashboard
                </a>
                <form method="POST" action="{{ route('site-settings.reset-defaults') }}" onsubmit="return confirm('Reset all role menu settings to defaults?')">
                    @csrf
                    <button type="submit" class="rounded-lg border border-red-300 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                        Reset To Defaults
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    @push('meta')
        <meta name="robots" content="noindex, nofollow, noarchive">
    @endpush

    <div class="space-y-6">
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            This page is intentionally hidden from the sidebar and intended for super admin use only.
        </div>

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900">Role and Available Menus</h3>
            <p class="mt-1 text-sm text-gray-500">Select one role, then choose which menus appear in the sidebar for that role.</p>

            <form method="GET" action="{{ route('site-settings.index') }}" class="mt-4">
                <label for="role" class="mb-1 block text-sm font-medium text-gray-700">Role</label>
                <div class="flex items-end gap-3">
                    <select id="role" name="role" class="w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-violet-500">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $selectedRole?->id === $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Load
                    </button>
                </div>
            </form>

            @if($selectedRole)
                <form method="POST" action="{{ route('site-settings.roles.update', $selectedRole) }}" class="mt-6 rounded-lg border border-gray-200 p-4">
                    @csrf

                    <div class="mb-3 flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-900">Available menus for <span class="text-violet-700">{{ $selectedRole->name }}</span></p>
                        <button type="submit" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-3 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">
                            Save
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($menuGroups as $groupName => $items)
                            <details class="rounded-lg border border-gray-200 bg-gray-50" open>
                                <summary class="cursor-pointer list-none px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-gray-900">{{ $groupName }}</p>
                                        <span class="text-xs text-gray-500">{{ count($items) }} menu{{ count($items) > 1 ? 's' : '' }}</span>
                                    </div>
                                </summary>

                                <div class="border-t border-gray-200 bg-white px-4 py-3">
                                    <ul class="space-y-2">
                                        @foreach($items as $menuKey => $menuItem)
                                            <li class="rounded-md border border-gray-100 bg-gray-50 p-2 pl-4">
                                                <label class="flex items-start gap-2 text-sm text-gray-700">
                                                    <input
                                                        type="checkbox"
                                                        name="menu_keys[]"
                                                        value="{{ $menuKey }}"
                                                        class="mt-0.5 rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                                                        {{ ($selectedRoleMenuMap[$menuKey] ?? false) ? 'checked' : '' }}
                                                    >
                                                    <span>
                                                        <span class="font-medium text-gray-900">{{ $menuItem['label'] ?? $menuKey }}</span>
                                                        <span class="block text-xs text-gray-500">{{ $menuItem['description'] ?? '' }}</span>
                                                    </span>
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </details>
                        @endforeach
                    </div>
                </form>
            @endif
        </section>
    </div>
</x-app-layout>
