<?php

use App\Models\User;

beforeEach(fn () => seedRbac());

test('guests cannot view the admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('authenticated staff can view the admin dashboard', function () {
    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Dashboard', false);
    $response->assertSee('Coffee &amp; Kala site overview', false);
    $response->assertSee('Gallery plates', false);
    $response->assertSee('Studio works', false);
    $response->assertSee('Journal entries', false);
    $response->assertSee('Feature chapters', false);
    $response->assertSee('Recent journal', false);
});
