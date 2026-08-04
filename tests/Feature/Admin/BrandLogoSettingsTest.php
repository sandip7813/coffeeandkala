<?php

use App\Models\User;
use App\Support\BrandLogo;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    seedRbac();
    Cache::flush();
});

test('guests cannot view brand settings', function () {
    $this->get(route('admin.settings.edit'))
        ->assertRedirect(route('login'));
});

test('admins without super admin role cannot view brand settings', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->get(route('admin.settings.edit'))
        ->assertForbidden();
});

test('editors cannot view brand settings', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)
        ->get(route('admin.settings.edit'))
        ->assertForbidden();
});

test('super admins can view both logo options', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('admin.settings.edit'))
        ->assertSuccessful()
        ->assertSee('Brand logo', false)
        ->assertSee('Wordmark', false)
        ->assertSee('Monogram', false)
        ->assertSee(asset('images/logo/logo-wordmark.png'), false)
        ->assertSee(asset('images/logo/gk-mark.png'), false)
        ->assertSee('Social media links', false);
});

test('super admins can switch the brand logo', function () {
    $user = User::factory()->superAdmin()->create();

    expect(BrandLogo::currentKey())->toBe('mark');

    $this->actingAs($user)
        ->put(route('admin.settings.logo.update'), [
            'logo' => 'wordmark',
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHas('status');

    expect(BrandLogo::currentKey())->toBe('wordmark')
        ->and(BrandLogo::path())->toBe('images/logo/logo-wordmark.png');

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('images/logo/logo-wordmark.png', false);
});

test('admins cannot update the brand logo', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->put(route('admin.settings.logo.update'), [
            'logo' => 'wordmark',
        ])
        ->assertForbidden();

    expect(BrandLogo::currentKey())->toBe('mark');
});

test('invalid logo keys are rejected', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->from(route('admin.settings.edit'))
        ->put(route('admin.settings.logo.update'), [
            'logo' => 'not-a-logo',
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHasErrors('logo');
});
