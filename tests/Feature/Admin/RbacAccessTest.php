<?php

use App\Models\User;

beforeEach(fn () => seedRbac());

test('guests cannot view the admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('users without roles cannot view the admin dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('editors can view the admin dashboard', function () {
    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Dashboard', false);
});

test('editors cannot manage users roles or permissions', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.roles.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.permissions.index'))->assertForbidden();
});

test('super admins can access administration screens', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->get(route('admin.users.index'))->assertSuccessful();
    $this->actingAs($user)->get(route('admin.roles.index'))->assertSuccessful();
    $this->actingAs($user)->get(route('admin.permissions.index'))->assertSuccessful();
});

test('users without admin access cannot log in to the panel', function () {
    $user = User::factory()->create();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('editors can authenticate and reach the dashboard', function () {
    $user = User::factory()->editor()->create();

    $response = $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('admin.dashboard'));
});

test('registration routes are disabled', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register')->assertNotFound();
});
