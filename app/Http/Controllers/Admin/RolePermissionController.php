<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        $users = User::with('roles')->orderBy('name')->get();
        $roles = Role::with('permissions')->where('guard_name', 'web')->orderBy('name')->get();
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.roles.index', compact('users', 'roles', 'permissions'));
    }

    public function editUser(User $user): View
    {
        $roles = Role::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.roles.edit-user', compact('user', 'roles'));
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $request->validate(['roles' => ['array'], 'roles.*' => ['string', 'exists:roles,name']]);

        $user->syncRoles($request->input('roles', []));

        return redirect()->route('admin.roles.index')->with('success', __('User roles updated.'));
    }

    public function editRole(Role $role): View
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.roles.edit-role', compact('role', 'permissions'));
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $request->validate(['permissions' => ['array'], 'permissions.*' => ['string', 'exists:permissions,name']]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', __('Role permissions updated.'));
    }
}
