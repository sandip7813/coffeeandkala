<?php

use App\Models\User;
use App\Support\SocialLinks;

test('filled social links omit blank urls', function () {
    SocialLinks::set([
        'instagram' => 'https://instagram.com/coffeeandkala',
        'facebook' => '',
        'pinterest' => '   ',
        'youtube' => '',
        'x' => 'https://x.com/coffeeandkala',
    ]);

    expect(SocialLinks::filled())->toHaveCount(2)
        ->and(collect(SocialLinks::filled())->pluck('key')->all())->toBe(['instagram', 'x']);
});

test('unknown network keys are ignored when saving', function () {
    SocialLinks::set([
        'instagram' => 'https://instagram.com/coffeeandkala',
        'tiktok' => 'https://tiktok.com/@coffeeandkala',
    ]);

    expect(SocialLinks::all())->toHaveKeys(['instagram', 'facebook', 'pinterest', 'youtube', 'x'])
        ->and(SocialLinks::all())->not->toHaveKey('tiktok');
});

test('settings page shows icon inputs for social networks including facebook', function () {
    seedRbac();

    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('admin.settings.edit'))
        ->assertSuccessful()
        ->assertSee('input-group-text settings-field-icon', false)
        ->assertSee('bi bi-instagram', false)
        ->assertSee('bi bi-facebook', false)
        ->assertSee('bi bi-pinterest', false)
        ->assertSee('bi bi-youtube', false)
        ->assertSee('bi bi-twitter-x', false)
        ->assertSee('visually-hidden', false);
});
