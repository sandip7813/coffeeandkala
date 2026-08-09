<?php

use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    seedRbac();
    Cache::flush();
});

test('no settings panel is expanded on a fresh visit', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('admin.settings.edit'))
        ->assertSuccessful()
        ->assertDontSee('accordion-collapse collapse show', false);
});

test('the brand logo panel reopens after it is saved', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->followingRedirects()
        ->put(route('admin.settings.logo.update'), ['logo' => 'wordmark'])
        ->assertSuccessful()
        ->assertSee('id="settings-logo-collapse" class="accordion-collapse collapse show"', false)
        ->assertDontSee('id="settings-social-collapse" class="accordion-collapse collapse show"', false)
        ->assertDontSee('id="settings-contact-collapse" class="accordion-collapse collapse show"', false);
});

test('the social links panel reopens after it is saved', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->followingRedirects()
        ->put(route('admin.settings.social.update'), [
            'links' => [
                'instagram' => 'https://instagram.com/coffeeandkala',
                'facebook' => '',
                'pinterest' => '',
                'youtube' => '',
                'x' => '',
            ],
        ])
        ->assertSuccessful()
        ->assertSee('id="settings-social-collapse" class="accordion-collapse collapse show"', false)
        ->assertDontSee('id="settings-logo-collapse" class="accordion-collapse collapse show"', false)
        ->assertDontSee('id="settings-contact-collapse" class="accordion-collapse collapse show"', false);
});

test('the contact panel reopens after it is saved', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->followingRedirects()
        ->put(route('admin.settings.contact.update'), [
            'contact' => [
                'email' => 'hello@coffeeandkala.com',
                'phone' => '',
                'address' => '',
            ],
        ])
        ->assertSuccessful()
        ->assertSee('id="settings-contact-collapse" class="accordion-collapse collapse show"', false)
        ->assertDontSee('id="settings-logo-collapse" class="accordion-collapse collapse show"', false)
        ->assertDontSee('id="settings-social-collapse" class="accordion-collapse collapse show"', false);
});

test('the contact panel reopens after invalid input fails validation', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->from(route('admin.settings.edit'))
        ->followingRedirects()
        ->put(route('admin.settings.contact.update'), [
            'contact' => [
                'email' => '',
                'phone' => 'asads',
                'address' => '',
            ],
        ])
        ->assertSuccessful()
        ->assertSee('id="settings-contact-collapse" class="accordion-collapse collapse show"', false)
        ->assertDontSee('id="settings-logo-collapse" class="accordion-collapse collapse show"', false)
        ->assertDontSee('id="settings-social-collapse" class="accordion-collapse collapse show"', false);
});
