<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\HomeSectionArticle;

function seedFeatureEdition(): array
{
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'art-culture')->firstOrFail();

    $highlighted = Article::factory()->feature()->active()->create([
        'category_id' => $category->id,
        'title' => 'A Highlighted Dispatch',
    ]);
    $edition = Article::factory()->feature()->active()->create([
        'category_id' => $category->id,
        'title' => 'An Edition Dispatch',
    ]);
    $recent = Article::factory()->feature()->active()->create([
        'category_id' => $category->id,
        'title' => 'A Recent Dispatch',
    ]);

    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_FEATURES_HIGHLIGHTED, 'article_id' => $highlighted->id, 'sort_order' => 0]);
    HomeSectionArticle::create(['section' => HomeSectionArticle::SECTION_FEATURES_EDITION, 'article_id' => $edition->id, 'sort_order' => 0]);

    return compact('highlighted', 'edition', 'recent');
}

test('the features page returns a successful response', function () {
    seedFeatureEdition();

    $response = $this->get(route('features'));

    $response->assertSuccessful();
    $response->assertDontSee('images/features/banner.png', false);
    $response->assertDontSee('featuresBannerZoom', false);
    $response->assertDontSee('features-banner-divider-mark', false);
    $response->assertSee('features-masthead', false);
    $response->assertSee('features-edition', false);
    $response->assertSee('Highlighted', false);
    $response->assertSee('A Highlighted Dispatch', false);
    $response->assertSee('More from this edition', false);
    $response->assertSee('An Edition Dispatch', false);
    $response->assertSee('A Recent Dispatch', false);
    $response->assertSee('data-journal-feature', false);
    $response->assertSee('data-home-carousel', false);
    $response->assertSee(route('features.show', 'art-culture', absolute: false), false);
    $response->assertSee('features-closing', false);
    $response->assertSee('Keep wandering', false);
    $html = $response->getContent();
    expect(
        str_contains($html, 'resources/css/features.css') || str_contains($html, 'build/assets/features-')
    )->toBeTrue();

    // Every sub-feature kicker (category label) is a link to its category page.
    expect($html)->toContain(
        '<a href="'.route('features.show', 'art-culture').'">Art &amp; Culture</a>'
    );

    // The old sidebar (columns/briefs/spotlight) is gone entirely — the
    // lower grid is a single full-width "last 12 updated" list now.
    $response->assertDontSee('features-spotlight', false);
    $response->assertDontSee('features-sidebar', false);
    $response->assertDontSee('features-pullquote', false);

    $response->assertDontSee('features-pagination', false);
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
});

test('the features page shows nothing published yet gracefully when no articles exist', function () {
    $response = $this->get(route('features'));

    $response->assertSuccessful();
    $response->assertDontSee('Highlighted', false);
    $response->assertDontSee('More from this edition', false);
    $response->assertDontSee('features-spotlight', false);
});

test('the Highlighted and More from this edition sections only show admin-picked articles', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'art-culture')->firstOrFail();
    $notPicked = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'Never Picked Dispatch']);

    $response = $this->get(route('features'));

    $response->assertSuccessful();
    $response->assertDontSee('Highlighted', false);
    $response->assertDontSee('More from this edition', false);
    // It can still appear via the "last 12 updated" grid, just not the two
    // curated sections above it.
    $response->assertSee($notPicked->title, false);
});

test('the "last 12 updated" grid shows the most recently updated feature articles', function () {
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', 'art-culture')->firstOrFail();
    $older = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'An Older Dispatch']);
    // Eloquent's update() re-touches updated_at to "now" regardless of what
    // the array says, so backdate it directly through the query builder.
    Article::whereKey($older->id)->update(['updated_at' => now()->subDays(5)]);
    $newer = Article::factory()->feature()->active()->create(['category_id' => $category->id, 'title' => 'A Newer Dispatch']);

    $response = $this->get(route('features'));

    $response->assertSuccessful();
    $html = $response->getContent();
    expect(strpos($html, $newer->title))->toBeLessThan(strpos($html, $older->title));
});

test('features links from the header and footer navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('features', absolute: false), false);
    $home->assertSee(route('features.show', 'art-culture', absolute: false), false);
});
