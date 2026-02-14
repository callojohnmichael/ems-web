<x-app-layout>
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Edit user</h2>
        <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="mt-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-violet-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-violet-500">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New password (optional)</label>
                    <input id="password" name="password" type="password" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-violet-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-violet-500 focus:ring-violet-500">
                </div>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-700">Roles</p>
                <div class="mt-2 grid gap-2 sm:grid-cols-2 md:grid-cols-3">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="roles[]" value="{{ $role->name }}" {{ in_array($role->name, old('roles', $user->roles->pluck('name')->all()), true) ? 'checked' : '' }} class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                            <span class="text-sm text-gray-700">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="skip_2fa" value="1" {{ old('skip_2fa', $user->skip_2fa) ? 'checked' : '' }} class="rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                <span class="text-sm text-gray-700">Skip 2FA for this user</span>
            </label>

            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:from-violet-700 hover:to-purple-700">Save changes</button>
                <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
