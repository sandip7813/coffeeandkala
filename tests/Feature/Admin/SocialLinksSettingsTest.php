<?php

use App\Models\User;
use App\Support\SocialLinks;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    seedRbac();
    Cache::flush();
});

test('super admins can save social media links', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->put(route('admin.settings.social.update'), [
            'links' => [
                'instagram' => 'https://instagram.com/coffeeandkala',
                'facebook' => '',
                'pinterest' => '',
                'youtube' => 'https://youtube.com/@coffeeandkala',
                'x' => '',
            ],
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHas('status');

    expect(SocialLinks::all())->toMatchArray([
        'instagram' => 'https://instagram.com/coffeeandkala',
        'facebook' => '',
        'pinterest' => '',
        'youtube' => 'https://youtube.com/@coffeeandkala',
        'x' => '',
    ]);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('https://instagram.com/coffeeandkala', false)
        ->assertSee('https://youtube.com/@coffeeandkala', false)
        ->assertDontSee('fa-facebook-f', false)
        ->assertDontSee('fa-pinterest-p', false)
        ->assertDontSee('fa-x-twitter', false);
});

test('admins cannot update social media links', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->put(route('admin.settings.social.update'), [
            'links' => [
                'instagram' => 'https://instagram.com/coffeeandkala',
            ],
        ])
        ->assertForbidden();
});

test('invalid social urls are rejected', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->from(route('admin.settings.edit'))
        ->put(route('admin.settings.social.update'), [
            'links' => [
                'instagram' => 'not-a-url',
                'facebook' => '',
                'pinterest' => '',
                'youtube' => '',
                'x' => '',
            ],
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHasErrors('links.instagram');
});

test('footer hides social section when no links are configured', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('footer-social', false);
});
