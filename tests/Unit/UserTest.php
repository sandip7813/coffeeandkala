<?php

use App\Models\User;

test('full_name combines first and last name', function () {
    $user = new User([
        'first_name' => 'Coffee',
        'last_name' => 'Kala',
    ]);

    expect($user->full_name)->toBe('Coffee Kala')
        ->and($user->name)->toBe('Coffee Kala');
});
