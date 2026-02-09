<x-app-layout>
    <div class="space-y-6">
        {{-- Users & Roles Section --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Users & roles</h2>
            <p class="mt-1 text-sm text-gray-500">Assign roles to users.</p>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Name</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Email</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Roles</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($users as $u)
                        <tr>
                            <td class="px-4 py-3 text-gray-900">{{ $u->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $u->email }}</td>
                            <td class="px-4 py-3">
                                @foreach($u->roles as $r)
                                    <span class="rounded bg-violet-100 px-2 py-0.5 text-xs text-violet-800">{{ $r->name }}</span>
                                @endforeach
                                @if($u->roles->isEmpty())
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.roles.edit-user', $u) }}" class="text-violet-600 hover:underline">Edit roles</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-4 text-gray-500 text-center">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Roles & Permissions Section --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Roles & permissions</h2>
            <p class="mt-1 text-sm text-gray-500">Assign permissions to roles.</p>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Role</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Permissions</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach($roles as $role)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $role->name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(5) as $p)
                                        <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ $p->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 5)
                                        <span class="text-gray-500 text-xs">+{{ $role->permissions->count() - 5 }} more</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.roles.edit-role', $role) }}" class="text-violet-600 hover:underline">Edit permissions</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>