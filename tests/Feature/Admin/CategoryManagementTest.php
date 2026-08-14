<?php

use App\Models\Category;
use App\Models\User;

beforeEach(fn () => seedRbac());

test('super admin can view the categories list', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => 'feature']);

    $this->actingAs($user)
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee($category->title);
});

test('super admin can edit a category', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => 'journal', 'title' => 'Old Title']);

    $response = $this->actingAs($user)->put(route('admin.categories.update', $category), [
        'title' => 'New Title',
        'slug' => $category->slug,
        'description' => 'Updated description',
        'sort_order' => 3,
        'status' => true,
    ]);

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'title' => 'New Title',
        'sort_order' => 3,
    ]);
});

test('slugs with uppercase, spaces, or underscores are rejected', function (string $slug) {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.categories.update', $category), [
        'title' => $category->title,
        'slug' => $slug,
        'sort_order' => 0,
        'status' => true,
    ]);

    $response->assertSessionHasErrors('slug');
})->with([
    'Art Culture',
    'art_culture',
    'art--culture',
    '-art-culture',
    'art-culture-',
]);

test('a well-formed slug is accepted', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.categories.update', $category), [
        'title' => $category->title,
        'slug' => 'art-culture-2',
        'sort_order' => 0,
        'status' => true,
    ]);

    $response->assertSessionDoesntHaveErrors('slug');
});

test('super admin can toggle a category status', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['status' => true]);

    $response = $this->actingAs($user)->put(route('admin.categories.status.update', $category));

    $response->assertRedirect(route('admin.categories.index'));

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'status' => false,
    ]);
});

test('categories list can be filtered by title, type and status', function () {
    $user = User::factory()->superAdmin()->create();

    $match = Category::factory()->create(['title' => 'Vineyard Tales', 'type' => 'feature', 'status' => true]);
    Category::factory()->create(['title' => 'Worth Knowing', 'type' => 'journal', 'status' => true]);
    Category::factory()->create(['title' => 'Vineyard Retreats', 'type' => 'feature', 'status' => false]);

    $response = $this->actingAs($user)->get(route('admin.categories.index', [
        'title' => 'Vineyard',
        'type' => 'feature',
        'status' => 'active',
    ]));

    $response->assertOk()
        ->assertSee($match->title)
        ->assertDontSee('Worth Knowing')
        ->assertDontSee('Vineyard Retreats');
});

test('a deactivated feature category 404s on the public site', function () {
    $category = Category::query()->where('type', 'feature')->where('slug', 'art-culture')->firstOrFail();
    $category->update(['status' => false]);

    $this->get(route('features.show', 'art-culture'))->assertNotFound();
});

test('a deactivated journal category 404s on the public site', function () {
    $category = Category::query()->where('type', 'journal')->where('slug', 'worth-knowing')->firstOrFail();
    $category->update(['status' => false]);

    $this->get(route('journal.category', 'worth-knowing'))->assertNotFound();
});

test('editors cannot manage categories', function () {
    $user = User::factory()->editor()->create();
    $category = Category::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.categories.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('admin.categories.update', $category), ['title' => 'x'])
        ->assertForbidden();

    $this->actingAs($user)
        ->put(route('admin.categories.status.update', $category))
        ->assertForbidden();
});
