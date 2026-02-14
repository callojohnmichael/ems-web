<?php

namespace App\Http\Controllers;

use App\Models\MenuAccessAudit;
use App\Models\RoleMenuAccess;
use App\Models\UserMenuAccess;
use App\Services\MenuAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class SiteSettingsController extends Controller
{
    public function __construct(private readonly MenuAccessService $menuAccessService)
    {
    }

    public function index(Request $request): View
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $menuGroups = $this->menuAccessService->groupedMenuItems();
        $selectedRole = null;

        if ($roles->isNotEmpty()) {
            $selectedRole = $roles->firstWhere('id', $request->integer('role')) ?? $roles->first();
        }

        $selectedRoleMenuMap = $selectedRole
            ? $this->menuAccessService->getRoleMenuMap($selectedRole->name)
            : [];

        return view('site-settings.index', [
            'hideSidebar' => true,
            'roles' => $roles,
            'selectedRole' => $selectedRole,
            'menuGroups' => $menuGroups,
            'selectedRoleMenuMap' => $selectedRoleMenuMap,
        ]);
    }

    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $menuKeys = $this->menuAccessService->menuKeys();
        $validated = $request->validate([
            'menu_keys' => ['array'],
            'menu_keys.*' => ['string', 'in:' . implode(',', $menuKeys)],
        ]);

        $enabledByKey = [];
        foreach ($menuKeys as $menuKey) {
            $enabledByKey[$menuKey] = in_array($menuKey, $validated['menu_keys'] ?? [], true);
        }

        $oldMap = $this->menuAccessService->getRoleMenuMap($role->name);
        $actorId = $request->user()?->id;

        DB::transaction(function () use ($role, $enabledByKey, $oldMap, $actorId): void {
            $this->menuAccessService->persistRoleMenuMap($role, $enabledByKey);

            foreach ($enabledByKey as $menuKey => $newValue) {
                $previous = (bool) ($oldMap[$menuKey] ?? false);
                if ($previous === $newValue) {
                    continue;
                }

                MenuAccessAudit::create([
                    'actor_user_id' => $actorId,
                    'target_type' => 'role',
                    'target_id' => $role->id,
                    'menu_key' => $menuKey,
                    'previous_value' => $previous,
                    'new_value' => $newValue,
                    'action' => 'role_menu_updated',
                    'meta' => ['role_name' => $role->name],
                ]);
            }
        });

        return redirect()
            ->route('site-settings.index', ['role' => $role->id])
            ->with('success', "Menu access updated for role: {$role->name}.");
    }

    public function resetDefaults(Request $request): RedirectResponse
    {
        $actorId = $request->user()?->id;

        DB::transaction(function () use ($actorId): void {
            RoleMenuAccess::query()->delete();
            UserMenuAccess::query()->delete();

            MenuAccessAudit::create([
                'actor_user_id' => $actorId,
                'target_type' => 'system',
                'target_id' => null,
                'menu_key' => null,
                'previous_value' => null,
                'new_value' => null,
                'action' => 'all_menu_settings_reset_to_defaults',
                'meta' => null,
            ]);
        });

        return redirect()
            ->route('site-settings.index')
            ->with('success', 'All menu access settings were reset to defaults.');
    }
}
