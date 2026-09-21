<?php

use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use App\Support\PageOneLiner;

beforeEach(fn () => seedRbac());

test('super admin can save closing one-liners with line breaks', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->put(route('admin.page-one-liners.update'), [
        'lines' => ['poetry' => "Read slowly.\r\nSome doors don't open twice.", 'gallery' => ''],
    ])->assertRedirect(route('admin.page-one-liners.edit'));

    expect(PageOneLiner::forPage('poetry'))->toBe("Read slowly.\nSome doors don't open twice.")
        ->and(PageOneLiner::forPage('gallery'))->toBeNull();
});

test('one-liners are limited to 150 characters', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->put(route('admin.page-one-liners.update'), [
        'lines' => ['poetry' => str_repeat('a', 151)],
    ])->assertSessionHasErrors('lines.poetry');
});

test('users without the permission cannot manage closing one-liners', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)->get(route('admin.page-one-liners.edit'))->assertForbidden();
    $this->actingAs($user)->put(route('admin.page-one-liners.update'), [])->assertForbidden();
});

test('a user with only the page content permission can manage them', function () {
    $user = userWithPermission(['view-dashboard', 'manage-page-content']);

    $this->actingAs($user)->get(route('admin.page-one-liners.edit'))->assertOk();
});

test('feature and journal categories can store a closing line', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => 'feature']);

    $this->actingAs($user)->get(route('admin.categories.edit', $category))
        ->assertOk()->assertSee('Closing one-liner');

    $this->actingAs($user)->put(route('admin.categories.update', $category), [
        'title' => $category->title,
        'slug' => $category->slug,
        'sort_order' => 0,
        'status' => 1,
        'closing_line' => 'Go where the map ends.',
    ])->assertRedirect(route('admin.categories.index'));

    expect($category->fresh()->closing_line)->toBe('Go where the map ends.');
});

test('static pages show their one-liner only when one is set', function (string $routeName, string $page, string $marker) {
    $this->get(route($routeName))->assertOk()->assertDontSee($marker);

    Setting::setValue(PageOneLiner::settingKey($page), 'A line for <b>'.$marker.'</b>');

    $this->get(route($routeName))->assertOk()
        ->assertSee('A line for &lt;b&gt;'.$marker.'&lt;/b&gt;', false);
})->with([
    ['about', 'our-story', 'zzOurStory'],
    ['features', 'features', 'zzFeatures'],
    ['journal', 'journal', 'zzJournal'],
    ['studio', 'studio', 'zzStudio'],
    ['gallery', 'gallery', 'zzGallery'],
    ['poetry', 'poetry', 'zzPoetry'],
]);

test('category pages show their own one-liner only when one is set', function (string $type, string $routeName, string $slug) {
    $category = Category::query()->where('type', $type)->where('slug', $slug)->first()
        ?? Category::factory()->create(['type' => $type, 'slug' => $slug]);
    $category->update(['status' => true, 'closing_line' => null]);

    $this->get(route($routeName, $slug))->assertOk()->assertDontSee('zzCategoryLine');

    $category->update(['closing_line' => "zzCategoryLine\nsecond"]);

    $this->get(route($routeName, $slug))->assertOk()->assertSee('zzCategoryLine<br />', false);
})->with([
    ['feature', 'features.show', 'art-culture'],
    ['journal', 'journal.category', 'worth-knowing'],
]);

test('category one-liners can be managed from the closing one-liners screen', function () {
    $user = User::factory()->superAdmin()->create();
    $feature = Category::factory()->create(['type' => 'feature', 'closing_line' => 'old line']);
    $journal = Category::factory()->create(['type' => 'journal', 'closing_line' => 'stays hidden']);

    $this->actingAs($user)->get(route('admin.page-one-liners.edit'))
        ->assertOk()->assertSee('Feature Categories')->assertSee('Journal Categories')->assertSee('old line');

    $this->actingAs($user)->put(route('admin.page-one-liners.update'), [
        'category_lines' => [$feature->id => "New\r\nline", $journal->id => ''],
    ])->assertRedirect(route('admin.page-one-liners.edit'));

    expect($feature->fresh()->closing_line)->toBe("New\nline")
        ->and($journal->fresh()->closing_line)->toBeNull();

    $this->actingAs($user)->put(route('admin.page-one-liners.update'), [
        'category_lines' => [$feature->id => str_repeat('a', 151)],
    ])->assertSessionHasErrors("category_lines.{$feature->id}");
});

test('category labels on the closing one-liners screen are escaped only once', function () {
    $user = User::factory()->superAdmin()->create();
    Category::factory()->create(['type' => 'feature', 'title' => 'Coffee & Classics Test']);

    $this->actingAs($user)->get(route('admin.page-one-liners.edit'))
        ->assertSee('Coffee &amp; Classics Test', false)
        ->assertDontSee('Coffee &amp;amp; Classics Test', false);
});
