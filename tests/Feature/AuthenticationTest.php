<?php

use App\Models\User;

beforeEach(fn () => seedRbac());

test('the login page returns a successful response', function () {
    $response = $this->get(route('login'));

    $response->assertSuccessful();
    $response->assertSee(__('adminlte.sign_in'), false);
    $response->assertDontSee(__('adminlte.register_new_membership'), false);
});

test('guests are redirected to login when visiting the admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('staff can authenticate using the login form', function () {
    $user = User::factory()->editor()->create();

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard'));
});

test('users cannot authenticate with invalid credentials', function () {
    $user = User::factory()->editor()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
