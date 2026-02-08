<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Edit permissions: {{ $role->name }}</h2>

        <form action="{{ route('admin.roles.update-role', $role) }}" method="POST" class="mt-6">
            @csrf
            @method('PUT')
            <div class="grid gap-2 sm:grid-cols-2 md:grid-cols-3">
                @foreach($permissions as $permission)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }} class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                    <span class="text-sm">{{ $permission->name }}</span>
                </label>
                @endforeach
            </div>
            <div class="mt-6 flex gap-3">
                <button type="submit" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">Save</button>
                <a href="{{ route('admin.roles.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
