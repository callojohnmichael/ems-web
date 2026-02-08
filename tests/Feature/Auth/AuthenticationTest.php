<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users are redirected to OTP verification after password login', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertRedirect(route('otp.verify'));
});

test('users can complete login after OTP verification', function () {
    $user = User::factory()->create();
    $otp = '123456';
    Cache::put('otp:user:'.$user->id, $otp, 60);

    $response = $this->withSession(['otp_user_id' => encrypt($user->id)])
        ->post(route('otp.verify.store'), ['otp' => $otp]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
