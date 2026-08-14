<?php

use App\Models\User;

beforeEach(fn () => seedRbac());

test('categories sits right below users in the sidebar and is marked active on its own page', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->get(route('admin.categories.index'));

    $response->assertOk();
    $response->assertSeeTextInOrder(['Users', 'Categories', 'Roles']);
    $response->assertSeeInOrder([
        'href="'.route('admin.categories.index').'"',
        'class="nav-link active"',
    ], false);
});

test('the users nav item is active on the users page and dashboard stays inactive', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->get(route('admin.users.index'));

    $response->assertOk();
    $response->assertSeeInOrder([
        'href="'.route('admin.users.index').'"',
        'class="nav-link active"',
    ], false);
    $response->assertDontSee('href="'.route('admin.dashboard').'"'."\n           class=\"nav-link active\"", false);
});

test('editors without manage-categories do not see the categories link', function () {
    $user = User::factory()->editor()->create();

    $response = $this->actingAs($user)->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertDontSee(route('admin.categories.index'), false);
});
