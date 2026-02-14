<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserSkip2faRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->paginate(12);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'skip_2fa' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'skip_2fa' => $request->boolean('skip_2fa'),
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()->route('admin.users.index')->with('user_success', __('User created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['array'],
            'roles.*' => ['string', 'exists:roles,name'],
            'skip_2fa' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'skip_2fa' => $request->boolean('skip_2fa'),
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);
        $user->syncRoles($validated['roles'] ?? []);

        return redirect()->route('admin.users.index')->with('user_success', __('User updated successfully.'));
    }

    /**
     * Update the skip 2FA setting for the specified user.
     */
    public function updateSkip2fa(UpdateUserSkip2faRequest $request, User $user)
    {
        $user->update([
            'skip_2fa' => $request->boolean('skip_2fa'),
        ]);

        return redirect()->back()->with('success', __('User 2FA skip setting updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', __('You cannot delete your own account.'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('user_success', __('User deleted successfully.'));
    }
}
