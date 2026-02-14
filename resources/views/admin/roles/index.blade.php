<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Roles</h2>
                <p class="mt-1 text-sm text-gray-500">List of roles with CRUD actions.</p>
            </div>
            <a href="{{ route('admin.roles.create-role') }}" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">
                Create role
            </a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Role</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Permission Count</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($roles as $role)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $role->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $role->permissions_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.roles.edit-role', $role) }}" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-violet-700 hover:bg-violet-50" title="Edit role">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </a>
                                    @if($role->name !== 'admin')
                                        <form method="POST" action="{{ route('admin.roles.destroy-role', $role) }}" onsubmit="return confirm('Delete this role?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-red-700 hover:bg-red-50" title="Delete role">
                                                <i class="bi bi-trash"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">No roles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>
</x-app-layout>
