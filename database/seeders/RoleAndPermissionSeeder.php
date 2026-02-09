<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached roles & permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | CLEAN OLD ROLE LINKS (PREVENT DUPLICATES)
        |--------------------------------------------------------------------------
        */
        DB::table('model_has_roles')->truncate();

        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS LIST
        |--------------------------------------------------------------------------
        */
        $permissions = [

            // ADMIN AREA
            'view admin dashboard',
            'manage approvals',
            'manage scheduling',
            'manage venues',
            'manage posts',
            'manage participants',
            'adjust events',
            'view reports',
            'send documents',

            // USER AREA
            'view user dashboard',
            'request events',
            'respond documents',
            'view requests status',
            'view calendar',
            'view schedule',
            'view posts',
            'create posts',
            'comment posts',
            'view program flow',
            'contact support',

            // MULTIMEDIA
            'view media dashboard',
            'manage all posts',
            'view schedule and events',
            'receive support',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE ROLES
        |--------------------------------------------------------------------------
        */
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $userRole  = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $mediaRole = Role::firstOrCreate(['name' => 'multimedia_staff', 'guard_name' => 'web']);

        /*
        |--------------------------------------------------------------------------
        | ASSIGN PERMISSIONS TO ROLES
        |--------------------------------------------------------------------------
        */

        // 🔥 ADMIN AUTO GETS ALL PERMISSIONS (future proof)
        $adminRole->syncPermissions(Permission::all());

        // 👤 USER PERMISSIONS
        $userRole->syncPermissions([
            'view user dashboard',
            'request events',
            'respond documents',
            'view requests status',
            'view calendar',
            'view schedule',
            'view posts',
            'create posts',
            'comment posts',
            'view program flow',
            'contact support',
        ]);

        // 🎬 MULTIMEDIA STAFF
        $mediaRole->syncPermissions([
            'view media dashboard',
            'manage all posts',
            'view schedule and events',
            'receive support',
        ]);

        /*
        |--------------------------------------------------------------------------
        | ASSIGN ROLES TO USERS (STRICT — NO STACKING)
        |--------------------------------------------------------------------------
        */

        $firstUser = User::orderBy('id')->first();

        if ($firstUser) {
            // First registered user becomes ADMIN only
            $firstUser->syncRoles([$adminRole]);
        }

        // All other users become USER only
        User::where('id', '!=', optional($firstUser)->id)
            ->get()
            ->each(function ($user) use ($userRole) {
                $user->syncRoles([$userRole]);
            });

        // Clear cache again after changes
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
