<?php

use App\Models\RoleMenuAccess;
use App\Models\User;
use Spatie\Permission\Models\Role;

test('non super admin cannot access site settings page', function () {
    $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->syncRoles([$userRole]);

    $response = $this->actingAs($user)->get(route('site-settings.index'));

    $response->assertForbidden();
});

test('super admin can access site settings page and sidebar is hidden', function () {
    $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->syncRoles([$adminRole]);

    $response = $this->actingAs($admin)->get(route('site-settings.index'));

    $response
        ->assertOk()
        ->assertSee('Site Settings')
        ->assertDontSee('Search menu...')
        ->assertSee('noindex, nofollow, noarchive');
});

test('role menu access can hide calendar menu link', function () {
    $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->syncRoles([$userRole]);

    RoleMenuAccess::create([
        'role_id' => $userRole->id,
        'menu_key' => 'calendar',
        'is_enabled' => false,
    ]);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk()->assertDontSee('Calendar');
});
