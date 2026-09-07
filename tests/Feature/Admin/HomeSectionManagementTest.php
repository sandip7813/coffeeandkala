<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\HomeSectionArticle;
use App\Models\User;

beforeEach(function () {
    seedRbac();
});

test('a super admin can view the home sections page', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('admin.home-sections.edit'))
        ->assertOk()
        ->assertSee('Home Page');
});

test('a user without manage-home-sections cannot view the page', function () {
    $user = userWithPermission('view-features');

    $this->actingAs($user)
        ->get(route('admin.home-sections.edit'))
        ->assertForbidden();
});

test('a super admin can pick articles for a section, in order', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $first = Article::factory()->feature()->active()->create(['category_id' => $category->id]);
    $second = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)
        ->put(route('admin.home-sections.update', HomeSectionArticle::SECTION_LATEST_PIECES), [
            'article_ids' => [$second->id, $first->id],
        ])
        ->assertRedirect(route('admin.home-sections.edit'));

    expect(
        HomeSectionArticle::query()->forSection(HomeSectionArticle::SECTION_LATEST_PIECES)->pluck('article_id')->all()
    )->toBe([$second->id, $first->id]);
});

test('saving a section replaces its previous picks entirely', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $old = Article::factory()->feature()->active()->create(['category_id' => $category->id]);
    $new = Article::factory()->feature()->active()->create(['category_id' => $category->id]);

    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_THE_SELECTION, 'article_id' => $old->id, 'sort_order' => 0]);

    $this->actingAs($user)->put(route('admin.home-sections.update', HomeSectionArticle::SECTION_THE_SELECTION), [
        'article_ids' => [$new->id],
    ]);

    $picks = HomeSectionArticle::query()->forSection(HomeSectionArticle::SECTION_THE_SELECTION)->pluck('article_id')->all();
    expect($picks)->toBe([$new->id]);
});

test('a pending (not yet active) article cannot be picked for a home section', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $pending = Article::factory()->feature()->create(['category_id' => $category->id]);

    $this->actingAs($user)->put(route('admin.home-sections.update', HomeSectionArticle::SECTION_JOURNAL), [
        'article_ids' => [$pending->id],
    ]);

    expect(HomeSectionArticle::query()->forSection(HomeSectionArticle::SECTION_JOURNAL)->count())->toBe(0);
});

test('the article search endpoint only matches active articles by title', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $match = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'A Story About Rain']);
    Article::factory()->feature()->create(['category_id' => $category->id, 'title' => 'Rainy Pending Draft']);

    $response = $this->actingAs($user)
        ->getJson(route('admin.home-sections.search-articles', ['q' => 'Rain']));

    $response->assertOk();
    $response->assertJsonCount(1, 'results');
    $response->assertSee($match->title);
});

test('the article search endpoint requires at least 3 characters', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->getJson(route('admin.home-sections.search-articles', ['q' => 'Ra']))
        ->assertOk()
        ->assertJson(['results' => []]);
});

test('searching within the Features section only returns feature articles', function () {
    $user = User::factory()->superAdmin()->create();
    $featureCategory = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $journalCategory = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $feature = Article::factory()->feature()->active()->create(['category_id' => $featureCategory->id, 'title' => 'A Coastal Story']);
    Article::factory()->journal()->active()->create(['category_id' => $journalCategory->id, 'title' => 'A Coastal Dispatch']);

    $response = $this->actingAs($user)->getJson(
        route('admin.home-sections.search-articles', ['q' => 'Coastal', 'section' => HomeSectionArticle::SECTION_FEATURES])
    );

    $response->assertOk();
    $response->assertJsonCount(1, 'results');
    $response->assertSee($feature->title);
});

test('searching within the Journal section only returns journal articles', function () {
    $user = User::factory()->superAdmin()->create();
    $featureCategory = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $journalCategory = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    Article::factory()->feature()->active()->create(['category_id' => $featureCategory->id, 'title' => 'A Mountain Story']);
    $journal = Article::factory()->journal()->active()->create(['category_id' => $journalCategory->id, 'title' => 'A Mountain Dispatch']);

    $response = $this->actingAs($user)->getJson(
        route('admin.home-sections.search-articles', ['q' => 'Mountain', 'section' => HomeSectionArticle::SECTION_JOURNAL])
    );

    $response->assertOk();
    $response->assertJsonCount(1, 'results');
    $response->assertSee($journal->title);
});

test('searching within Latest Pieces or The Selection returns either article type', function () {
    $user = User::factory()->superAdmin()->create();
    $featureCategory = Category::factory()->create(['type' => Category::TYPE_FEATURE]);
    $journalCategory = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    Article::factory()->feature()->active()->create(['category_id' => $featureCategory->id, 'title' => 'A Desert Story']);
    Article::factory()->journal()->active()->create(['category_id' => $journalCategory->id, 'title' => 'A Desert Dispatch']);

    $response = $this->actingAs($user)->getJson(
        route('admin.home-sections.search-articles', ['q' => 'Desert', 'section' => HomeSectionArticle::SECTION_LATEST_PIECES])
    );

    $response->assertOk();
    $response->assertJsonCount(2, 'results');
});

test('a journal article cannot be saved into the Features section even by a hand-crafted request', function () {
    $user = User::factory()->superAdmin()->create();
    $category = Category::factory()->create(['type' => Category::TYPE_JOURNAL]);
    $journal = Article::factory()->journal()->active()->create(['category_id' => $category->id]);

    $this->actingAs($user)->put(route('admin.home-sections.update', HomeSectionArticle::SECTION_FEATURES), [
        'article_ids' => [$journal->id],
    ]);

    expect(HomeSectionArticle::query()->forSection(HomeSectionArticle::SECTION_FEATURES)->count())->toBe(0);
});

test('an unknown section name 404s', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->put(route('admin.home-sections.update', 'not-a-real-section'), ['article_ids' => []])
        ->assertNotFound();
});
