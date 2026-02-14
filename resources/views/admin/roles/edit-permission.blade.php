<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Edit permission</h2>
        <p class="mt-1 text-sm text-gray-500">Update permission name.</p>

        <form action="{{ route('admin.roles.permissions.update', $permission) }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Permission name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $permission->name) }}" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-violet-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">Save</button>
                <a href="{{ route('admin.roles.permissions.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
