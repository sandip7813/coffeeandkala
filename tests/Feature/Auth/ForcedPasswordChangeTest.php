<?php

use App\Models\User;

beforeEach(fn () => seedRbac());

test('login with a one-time password forces a password change', function () {
    $user = User::factory()->editor()->create([
        'password' => 'temp-password',
        'must_change_password' => true,
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'temp-password',
    ])->assertRedirect(route('password.force.edit'));

    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('password.force.edit'));

    $this->put(route('password.force.update'), [
        'current_password' => 'temp-password',
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ])->assertRedirect(route('admin.dashboard'));

    expect($user->fresh()->must_change_password)->toBeFalse();

    $this->get(route('admin.dashboard'))->assertSuccessful();
});
