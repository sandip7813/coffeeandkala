<?php

use App\Models\User;

beforeEach(fn () => seedRbac());

test('guests cannot view the profile page', function () {
    $this->get(route('admin.profile.edit'))
        ->assertRedirect(route('login'));
});

test('authenticated staff can view edit profile and change password pages', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)
        ->get(route('admin.profile.edit'))
        ->assertSuccessful()
        ->assertSee('Edit Profile', false)
        ->assertSee('First name', false)
        ->assertSee('Last name', false)
        ->assertSee('Phone number', false)
        ->assertSee($user->email, false)
        ->assertSee('justify-content-center', false);

    $this->actingAs($user)
        ->get(route('admin.profile.password.edit'))
        ->assertSuccessful()
        ->assertSee('Change Password', false)
        ->assertSee('justify-content-center', false);
});

test('authenticated staff can update their profile without changing email', function () {
    $user = User::factory()->editor()->create([
        'first_name' => 'Old',
        'last_name' => 'Name',
        'email' => 'old@example.com',
        'phone' => null,
    ]);

    $this->actingAs($user)
        ->put(route('admin.profile.update'), [
            'first_name' => 'New',
            'last_name' => 'Person',
            'email' => 'new@example.com',
            'phone' => '9876543210',
        ])
        ->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHas('status');

    $this->actingAs($user)
        ->withSession(['status' => 'Profile updated.'])
        ->get(route('admin.profile.edit'))
        ->assertSuccessful()
        ->assertSee('id="admin-flash"', false)
        ->assertSee('Profile updated.', false);

    expect($user->fresh())
        ->first_name->toBe('New')
        ->last_name->toBe('Person')
        ->full_name->toBe('New Person')
        ->email->toBe('old@example.com')
        ->phone->toBe('9876543210');
});

test('phone number must be numeric and at most 10 digits', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->put(route('admin.profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => '12ab',
        ])
        ->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHasErrors('phone');

    $this->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->put(route('admin.profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => '12345678901',
        ])
        ->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHasErrors('phone');
});

test('phone number is optional when updating profile', function () {
    $user = User::factory()->editor()->create([
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'phone' => '5551111111',
    ]);

    $this->actingAs($user)
        ->put(route('admin.profile.update'), [
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'phone' => '',
        ])
        ->assertRedirect(route('admin.profile.edit'));

    expect($user->fresh()->phone)->toBeNull();
});

test('authenticated staff can change their password', function () {
    $user = User::factory()->editor()->create([
        'password' => 'old-password',
    ]);

    $this->actingAs($user)
        ->put(route('admin.profile.password.update'), [
            'current_password' => 'old-password',
            'password' => 'new-password-secure',
            'password_confirmation' => 'new-password-secure',
        ])
        ->assertRedirect(route('admin.profile.password.edit'))
        ->assertSessionHas('status');

    expect(auth()->attempt([
        'email' => $user->email,
        'password' => 'new-password-secure',
    ]))->toBeTrue();
});

test('admin layout shows user menu and sidebar account links', function () {
    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee(route('admin.profile.edit', absolute: false), false);
    $response->assertSee(route('admin.profile.password.edit', absolute: false), false);
    $response->assertSee('bi-person-circle', false);
    $response->assertSee('sidebar-user-footer', false);
    $response->assertDontSee('adminlte-search-trigger', false);
    $response->assertDontSee('data-lte-toggle="fullscreen"', false);
});
