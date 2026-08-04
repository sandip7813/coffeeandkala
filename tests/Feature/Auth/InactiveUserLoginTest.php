<?php

use App\Models\User;

beforeEach(fn () => seedRbac());

test('inactive users cannot log in', function () {
    $user = User::factory()->editor()->create([
        'password' => 'password',
        'is_active' => false,
    ]);

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});
