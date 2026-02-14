<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Create role</h2>
        <p class="mt-1 text-sm text-gray-500">Add a new role and optionally assign permissions.</p>

        <form action="{{ route('admin.roles.store-role') }}" method="POST" class="mt-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Role name</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-violet-500"
                    placeholder="e.g. event_manager"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <p class="text-sm font-medium text-gray-700">Permissions</p>
                <div class="mt-2 grid gap-2 sm:grid-cols-2 md:grid-cols-3">
                    @foreach($permissions as $permission)
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->name }}"
                                {{ in_array($permission->name, old('permissions', []), true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-violet-600 focus:ring-violet-500"
                            >
                            <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('permissions.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">Create role</button>
                <a href="{{ route('admin.roles.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
