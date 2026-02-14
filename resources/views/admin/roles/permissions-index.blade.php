<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Permissions</h2>
                <p class="mt-1 text-sm text-gray-500">List of permissions with CRUD actions.</p>
            </div>
            <a href="{{ route('admin.roles.create-permission') }}" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">
                Create permission
            </a>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Permission</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($permissions as $permission)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $permission->name }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('admin.roles.permissions.edit', $permission) }}" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-violet-700 hover:bg-violet-50" title="Edit permission">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.roles.permissions.destroy', $permission) }}" onsubmit="return confirm('Delete this permission?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 rounded-md px-2 py-1 text-red-700 hover:bg-red-50" title="Delete permission">
                                            <i class="bi bi-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-500">No permissions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $permissions->links() }}
        </div>
    </div>
</x-app-layout>
