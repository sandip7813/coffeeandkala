<?php

use App\Models\User;
use App\Support\ContactInfo;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    seedRbac();
    Cache::flush();
});

test('super admins can save contact information', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->put(route('admin.settings.contact.update'), [
            'contact' => [
                'email' => 'hello@coffeeandkala.com',
                'phone' => '+91 98765 43210',
                'address' => '',
            ],
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHas('status');

    expect(ContactInfo::all())->toMatchArray([
        'email' => 'hello@coffeeandkala.com',
        'phone' => '+91 98765 43210',
        'address' => '',
    ]);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('hello@coffeeandkala.com')
        ->assertSee('+91 98765 43210');
});

test('admins cannot update contact information', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->put(route('admin.settings.contact.update'), [
            'contact' => [
                'email' => 'hello@coffeeandkala.com',
            ],
        ])
        ->assertForbidden();
});

test('invalid contact email is rejected', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->from(route('admin.settings.edit'))
        ->put(route('admin.settings.contact.update'), [
            'contact' => [
                'email' => 'not-an-email',
                'phone' => '',
                'address' => '',
            ],
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHasErrors('contact.email');
});

test('non-numeric phone values are rejected', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->from(route('admin.settings.edit'))
        ->put(route('admin.settings.contact.update'), [
            'contact' => [
                'email' => '',
                'phone' => 'asads',
                'address' => '',
            ],
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHasErrors('contact.phone');
});

test('phone numbers outside the 7-15 digit range are rejected', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->from(route('admin.settings.edit'))
        ->put(route('admin.settings.contact.update'), [
            'contact' => [
                'email' => '',
                'phone' => '12345',
                'address' => '',
            ],
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHasErrors('contact.phone');
});

test('a well-formed phone number is accepted', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->put(route('admin.settings.contact.update'), [
            'contact' => [
                'email' => '',
                'phone' => '+1 (415) 555-0132',
                'address' => '',
            ],
        ])
        ->assertRedirect(route('admin.settings.edit'))
        ->assertSessionHasNoErrors();

    expect(ContactInfo::all()['phone'])->toBe('+1 (415) 555-0132');
});

test('footer hides contact section when no details are configured', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertDontSee('footer-contact', false);
});
