<x-app-layout>
    <div
        class="space-y-6"
        x-data="{
            deleteModalOpen: false,
            deleteAction: '',
            deleteUserName: '',
            toastOpen: {{ session('user_success') ? 'true' : 'false' }},
            confirmDelete(action, name) {
                this.deleteAction = action;
                this.deleteUserName = name;
                this.deleteModalOpen = true;
            }
        }"
        x-init="if (toastOpen) { setTimeout(() => toastOpen = false, 3000) }"
    >
        @if(session('user_success'))
            <div
                x-show="toastOpen"
                x-transition
                class="fixed bottom-6 right-6 z-50 max-w-sm rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 shadow-lg"
                style="display: none;"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-green-600"></i>
                        <span>{{ session('user_success') }}</span>
                    </div>
                    <button type="button" @click="toastOpen = false" class="text-green-700 hover:text-green-900">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Users</h2>
                    <p class="mt-1 text-sm text-gray-500">Manage user accounts.</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">
                    Create user
                </a>
            </div>
        </div>

        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Name</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Email</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Roles</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">2FA Skip</th>
                        <th class="px-4 py-2 text-left font-medium text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-4 py-3 text-gray-900">{{ $user->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @forelse($user->roles as $role)
                                    <span class="rounded bg-violet-100 px-2 py-0.5 text-xs text-violet-800">{{ $role->name }}</span>
                                @empty
                                    <span class="text-gray-400">—</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->skip_2fa ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button 
                                            @click="confirmDelete(`{{ route('admin.users.destroy', $user) }}`, `{{ $user->name }}`)"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium"
                                        >
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>

        <div
            x-show="deleteModalOpen"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
            style="display: none;"
        >
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-red-100 text-red-600">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Delete user</h3>
                        <p class="text-sm text-gray-600">This action cannot be undone.</p>
                    </div>
                </div>

                <p class="mt-4 text-sm text-gray-700">
                    Are you sure you want to delete
                    <span class="font-semibold text-gray-900" x-text="deleteUserName"></span>?
                </p>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <form id="deleteUserForm" method="POST" :action="deleteAction">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
