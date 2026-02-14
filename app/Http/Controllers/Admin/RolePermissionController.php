<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('permissions')
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->paginate(12);

        return view('admin.roles.index', compact('roles'));
    }

    public function createRole(): View
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.roles.create-role', compact('permissions'));
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.roles.index')->with('success', __('Role created successfully.'));
    }

    public function editRole(Role $role): View
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.roles.edit-role', compact('role', 'permissions'));
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')->with('error', __('The admin role cannot be deleted.'));
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('Role deleted successfully.'));
    }

    public function permissionsIndex(): View
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->paginate(12);

        return view('admin.roles.permissions-index', compact('permissions'));
    }

    public function createPermission(): View
    {
        return view('admin.roles.create-permission');
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()->route('admin.roles.permissions.index')->with('success', __('Permission created successfully.'));
    }

    public function editPermission(Permission $permission): View
    {
        return view('admin.roles.edit-permission', compact('permission'));
    }

    public function updatePermission(Request $request, Permission $permission): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $permission->id],
        ]);

        $permission->update([
            'name' => $validated['name'],
        ]);

        return redirect()->route('admin.roles.permissions.index')->with('success', __('Permission updated successfully.'));
    }

    public function destroyPermission(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route('admin.roles.permissions.index')->with('success', __('Permission deleted successfully.'));
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $request->validate(['permissions' => ['array'], 'permissions.*' => ['string', 'exists:permissions,name']]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', __('Role permissions updated.'));
    }
}
