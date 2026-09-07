<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Meta;
use App\Models\Poem;
use App\Models\User;

beforeEach(function () {
    seedRbac();
});

test('a super admin can view the meta data page', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('admin.meta.edit'))
        ->assertOk()
        ->assertSee('Meta Data')
        ->assertDontSee('title)" />', false)
        ->assertDontSee('keywords)" />', false);
});

test('a user without manage-meta cannot view the meta data page', function () {
    $user = userWithPermission('view-features');

    $this->actingAs($user)
        ->get(route('admin.meta.edit'))
        ->assertForbidden();
});

test('a super admin can update a static page\'s meta data', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->put(route('admin.meta.page.update', 'home'), [
        'meta_title' => 'Home Title',
        'meta_description' => 'Home description.',
        'meta_keywords' => 'home, kala',
    ])->assertRedirect(route('admin.meta.edit'));

    $meta = Meta::forPage('home');
    expect($meta->title)->toBe('Home Title');
    expect($meta->description)->toBe('Home description.');
    expect($meta->keywords)->toBe('home, kala');
});

test('the features, journal, and poetry index pages\' own meta lives under the Main Pages tab', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->put(route('admin.meta.page.update', 'features'), [
        'meta_title' => 'Features Index Title',
    ])->assertRedirect(route('admin.meta.edit'));

    expect(Meta::forPage('features')->title)->toBe('Features Index Title');

    $response = $this->actingAs($user)->get(route('admin.meta.edit'));
    $response->assertOk();
    $response->assertSee('id="tab-main"', false);
    $response->assertSee('id="meta-features-collapse"', false);
    $response->assertSee('Features Index Title', false);
});

test('an unknown static page key 404s', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->put(route('admin.meta.page.update', 'not-a-real-page'), ['meta_title' => 'Nope'])
        ->assertNotFound();
});

test('the meta data page lists feature, journal, and poem content pages', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $feature = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'A Coastal Story']);
    $journalCategory = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $journal = Article::factory()->journal()->active()->create(['category_id' => $journalCategory->id, 'title' => 'A Coastal Dispatch']);
    $poem = Poem::factory()->create(['title' => 'A Quiet Verse']);

    $response = $this->actingAs($user)->get(route('admin.meta.edit'));

    $response->assertOk();
    $response->assertSee($feature->title);
    $response->assertSee($journal->title);
    $response->assertSee($poem->title);
});

test('the meta data page lists feature and journal category pages too', function () {
    $user = User::factory()->superAdmin()->create();
    $featureCategory = Category::factory()->create(['type' => Category::TYPE_FEATURE, 'title' => 'Weekend Trips']);
    $journalCategory = Category::factory()->create(['type' => Category::TYPE_JOURNAL, 'title' => 'Slow Mornings']);

    $response = $this->actingAs($user)->get(route('admin.meta.edit'));

    $response->assertOk();
    $response->assertSee('Feature Categories');
    $response->assertSee($featureCategory->title);
    $response->assertSee('Journal Categories');
    $response->assertSee($journalCategory->title);
});

test('a super admin can update a feature category\'s meta data from the meta data page', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);

    $this->actingAs($user)->put(route('admin.meta.content.update', ['feature-categories', $category->id]), [
        'meta_title' => 'Custom Category Title',
        'meta_description' => 'Custom category description.',
        'meta_keywords' => 'custom, category, keywords',
    ])->assertRedirect(route('admin.meta.edit'));

    $meta = $category->meta()->first();
    expect($meta->title)->toBe('Custom Category Title');
});

test('a super admin can update a journal category\'s meta data from the meta data page', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);

    $this->actingAs($user)->put(route('admin.meta.content.update', ['journal-categories', $category->id]), [
        'meta_title' => 'Custom Journal Category Title',
    ])->assertRedirect(route('admin.meta.edit'));

    expect($category->meta()->first()->title)->toBe('Custom Journal Category Title');
});

test('a journal category cannot be updated via the feature-categories content-meta type', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);

    $this->actingAs($user)
        ->put(route('admin.meta.content.update', ['feature-categories', $category->id]), ['meta_title' => 'Nope'])
        ->assertNotFound();
});

test('a super admin can update a feature article\'s meta data from the meta data page', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)->put(route('admin.meta.content.update', ['features', $article->id]), [
        'meta_title' => 'Custom Title',
        'meta_description' => 'Custom description.',
        'meta_keywords' => 'custom, keywords',
    ])->assertRedirect(route('admin.meta.edit'));

    $meta = $article->meta()->first();
    expect($meta->title)->toBe('Custom Title');
});

test('a super admin can update a poem\'s meta data from the meta data page', function () {
    $user = User::factory()->superAdmin()->create();
    $poem = Poem::factory()->create();

    $this->actingAs($user)->put(route('admin.meta.content.update', ['poetry', $poem->id]), [
        'meta_title' => 'Poem Title',
    ])->assertRedirect(route('admin.meta.edit'));

    expect($poem->meta()->first()->title)->toBe('Poem Title');
});

test('a journal article cannot be updated via the features content-meta type', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $journal = Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->put(route('admin.meta.content.update', ['features', $journal->id]), ['meta_title' => 'Nope'])
        ->assertNotFound();
});

test('the our story edit page can save its own SEO meta', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->get(route('admin.our-story.edit'))->assertOk();

    $this->actingAs($user)->put(route('admin.our-story.meta.update'), [
        'meta_title' => 'Our Story Title',
    ])->assertRedirect(route('admin.our-story.edit'));

    expect(Meta::forPage('our-story')->title)->toBe('Our Story Title');
});

test('the home page sections edit page can save its own SEO meta', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->put(route('admin.home-sections.meta.update'), [
        'meta_title' => 'Home SEO Title',
    ])->assertRedirect(route('admin.home-sections.edit'));

    expect(Meta::forPage('home')->title)->toBe('Home SEO Title');
});

test('a new article automatically gets its own meta row', function () {
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $article = Article::factory()->feature()->create(['category_id' => $category->id]);

    expect($article->meta()->exists())->toBeTrue();
});

test('a new poem automatically gets its own meta row', function () {
    $poem = Poem::factory()->create();

    expect($poem->meta()->exists())->toBeTrue();
});

test('the poetry admin page can save its own SEO meta', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)->put(route('admin.poetry.meta.update'), [
        'meta_title' => 'Poetry SEO Title',
    ])->assertRedirect(route('admin.poetry.index'));

    expect(Meta::forPage('poetry')->title)->toBe('Poetry SEO Title');
});

test('the poetry admin page shows an SEO tab to anyone who can edit poetry, not just manage-meta', function () {
    $user = userWithPermission(['view-poetry', 'edit-poetry']);

    $response = $this->actingAs($user)->get(route('admin.poetry.index'));

    $response->assertOk();
    $response->assertSee('tab-seo', false);

    $this->actingAs($user)->put(route('admin.poetry.meta.update'), [
        'meta_title' => 'Poetry SEO Title',
    ])->assertRedirect(route('admin.poetry.index'));

    expect(Meta::forPage('poetry')->title)->toBe('Poetry SEO Title');
});

test('the poetry admin page hides the SEO tab from a user who cannot edit poetry', function () {
    $user = userWithPermission(['view-poetry']);

    $response = $this->actingAs($user)->get(route('admin.poetry.index'));

    $response->assertOk();
    $response->assertDontSee('tab-seo', false);

    $this->actingAs($user)
        ->put(route('admin.poetry.meta.update'), ['meta_title' => 'Nope'])
        ->assertForbidden();
});

test('the features content list pages via ajax without more than 10 items on the first page', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    Article::factory()->feature()->active()->count(15)->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->getJson(route('admin.meta.content.list', 'features'));

    $response->assertOk();
    $html = $response->json('html');
    expect(substr_count($html, 'meta-features-'))->toBeGreaterThan(0);
    expect($html)->toContain('data-meta-page="2"');
});

test('the second page of the features content list returns the remaining items', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    Article::factory()->feature()->active()->count(15)->create(['category_id' => $category->id]);

    $response = $this->actingAs($user)->getJson(route('admin.meta.content.list', ['features', 'page' => 2]));

    $response->assertOk();
    expect($response->json('html'))->toContain('data-meta-page="1"');
});

test('a user without manage-meta cannot fetch a content list page', function () {
    $user = userWithPermission('view-features');

    $this->actingAs($user)
        ->getJson(route('admin.meta.content.list', 'features'))
        ->assertForbidden();
});
