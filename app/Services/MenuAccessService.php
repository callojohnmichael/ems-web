<?php

namespace App\Services;

use App\Models\RoleMenuAccess;
use App\Models\User;
use App\Models\UserMenuAccess;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;

class MenuAccessService
{
    public function menuItems(): array
    {
        return (array) config('site_settings.menus', []);
    }

    public function menuKeys(): array
    {
        return array_keys($this->menuItems());
    }

    public function groupedMenuItems(): array
    {
        $grouped = [];

        foreach ($this->menuItems() as $key => $item) {
            $group = (string) ($item['group'] ?? 'Other');
            $grouped[$group][$key] = $item;
        }

        return $grouped;
    }

    public function defaultForRole(string $roleName, string $menuKey): bool
    {
        $menu = $this->menuItems()[$menuKey] ?? null;

        if (! $menu) {
            return false;
        }

        return (bool) Arr::get($menu, 'defaults.' . $roleName, false);
    }

    public function getRoleMenuMap(string $roleName): array
    {
        $role = Role::query()
            ->where('guard_name', 'web')
            ->where('name', $roleName)
            ->first();

        $stored = [];

        if ($role) {
            $stored = RoleMenuAccess::query()
                ->where('role_id', $role->id)
                ->pluck('is_enabled', 'menu_key')
                ->map(fn ($value) => (bool) $value)
                ->all();
        }

        $map = [];
        foreach ($this->menuKeys() as $menuKey) {
            $map[$menuKey] = array_key_exists($menuKey, $stored)
                ? $stored[$menuKey]
                : $this->defaultForRole($roleName, $menuKey);
        }

        return $map;
    }

    public function isEnabledForRole(string $roleName, string $menuKey): bool
    {
        return (bool) ($this->getRoleMenuMap($roleName)[$menuKey] ?? false);
    }

    public function getUserOverrideMap(User $user): array
    {
        return UserMenuAccess::query()
            ->where('user_id', $user->id)
            ->pluck('is_enabled', 'menu_key')
            ->map(fn ($value) => (bool) $value)
            ->all();
    }

    public function getVisibilityMapForUser(User $user): array
    {
        $menuKeys = $this->menuKeys();
        $roleNames = $user->getRoleNames()->toArray();
        $roleMaps = [];

        foreach ($roleNames as $roleName) {
            $roleMaps[$roleName] = $this->getRoleMenuMap($roleName);
        }

        $visibility = [];

        foreach ($menuKeys as $menuKey) {
            $enabledByRole = false;

            if (count($roleMaps) > 0) {
                foreach ($roleMaps as $roleMap) {
                    if (($roleMap[$menuKey] ?? false) === true) {
                        $enabledByRole = true;
                        break;
                    }
                }
            }

            $visibility[$menuKey] = $enabledByRole && $this->isMenuAllowedBySystemRules($user, $menuKey);
        }

        return $visibility;
    }

    public function isVisibleForUser(User $user, string $menuKey): bool
    {
        return (bool) ($this->getVisibilityMapForUser($user)[$menuKey] ?? false);
    }

    public function persistRoleMenuMap(Role $role, array $enabledByKey): void
    {
        foreach ($this->menuKeys() as $menuKey) {
            $enabled = (bool) ($enabledByKey[$menuKey] ?? false);
            $default = $this->defaultForRole($role->name, $menuKey);

            if ($enabled === $default) {
                RoleMenuAccess::query()
                    ->where('role_id', $role->id)
                    ->where('menu_key', $menuKey)
                    ->delete();
                continue;
            }

            RoleMenuAccess::query()->updateOrCreate(
                ['role_id' => $role->id, 'menu_key' => $menuKey],
                ['is_enabled' => $enabled]
            );
        }
    }

    public function persistUserOverrideMap(User $user, array $enabledByKey): void
    {
        foreach ($this->menuKeys() as $menuKey) {
            if (! array_key_exists($menuKey, $enabledByKey)) {
                UserMenuAccess::query()
                    ->where('user_id', $user->id)
                    ->where('menu_key', $menuKey)
                    ->delete();
                continue;
            }

            UserMenuAccess::query()->updateOrCreate(
                ['user_id' => $user->id, 'menu_key' => $menuKey],
                ['is_enabled' => (bool) $enabledByKey[$menuKey]]
            );
        }
    }

    private function isMenuAllowedBySystemRules(User $user, string $menuKey): bool
    {
        return match ($menuKey) {
            'dashboard',
            'calendar',
            'events',
            'program_flow' => true,

            'event_check_in' => $user->can('event check-in access'),
            'participants' => $user->hasRole('admin'),
            'attendance' => $user->hasRole('admin') && $user->can('manage participants'),
            'multimedia' => $user->can('view multimedia'),

            'venues',
            'documents',
            'notifications',
            'support' => $user->hasRole('admin'),

            'reports_overview',
            'reports_pipeline',
            'reports_participants',
            'reports_venues',
            'reports_finance',
            'reports_engagement',
            'reports_support' => $user->hasRole('admin') && $user->can('view reports'),

            'users',
            'roles',
            'permissions' => $user->hasRole('admin'),

            default => false,
        };
    }
}
