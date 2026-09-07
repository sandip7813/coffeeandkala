<?php

use App\Models\Article;
use App\Models\Category;
use App\Support\FeatureCatalog;
use Illuminate\Support\Str;

/**
 * A real, active article for the category — with an introduction long
 * enough to exercise both the 80-char and 500-char excerpt-truncation
 * theme variants.
 */
function seedFeatureCategoryArticle(string $slug): Article
{
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', $slug)->firstOrFail();

    return Article::factory()->feature()->active()->create([
        'category_id' => $category->id,
        'title' => "The Story of {$slug}",
        'introduction' => str_repeat('A long introduction paragraph for excerpt truncation testing purposes. ', 10),
    ]);
}

test('each feature category page returns a successful response', function (string $slug) {
    $category = FeatureCatalog::find($slug);
    $article = seedFeatureCategoryArticle($slug);

    expect($category)->not->toBeNull();

    $response = $this->get(route('features.show', $slug));

    $response->assertSuccessful();
    $response->assertSee(e($category['name']), false);
    $response->assertSee($category['lead'], false);
    $response->assertSee($category['tagline'], false);
    $response->assertSee($category['quote'], false);
    $response->assertSee($article->title, false);
    $response->assertSee('features-theme--'.$slug, false);
    $response->assertDontSee(asset($category['banner']), false);
    $response->assertDontSee('categoryBannerZoom', false);
    $response->assertSee('fc-siblings', false);
    $response->assertSee(route('features', absolute: false), false);
})->with(FeatureCatalog::slugs());

test('the feature category pagination is dynamic and only appears once there is more than a page of articles', function () {
    $slug = 'on-a-budget';
    $category = Category::query()->ofType(Category::TYPE_FEATURE)->where('slug', $slug)->firstOrFail();

    seedFeatureCategoryArticle($slug);
    $this->get(route('features.show', $slug))->assertDontSee('fc-pagination', false);

    Article::factory()->feature()->active()->count(6)->create(['category_id' => $category->id]);

    $response = $this->get(route('features.show', $slug));
    $response->assertSee('fc-pagination', false);
    $response->assertSee('Page 1 of 2', false);

    $this->get(route('features.show', ['category' => $slug, 'page' => 2]))
        ->assertSee('Page 2 of 2', false);
});

test('a feature category with no articles shows a no-data message instead of the article list', function () {
    $slug = 'on-a-budget';

    $response = $this->get(route('features.show', $slug));

    $response->assertSuccessful();
    $response->assertSee('No articles found in this chapter yet.', false);
    $response->assertDontSee('fc-budget-grid', false);
    $response->assertDontSee('fc-pagination', false);
});

test('each feature category page uses a distinct theme layout', function () {
    $markers = [
        'art-culture' => 'fc-art-catalogue',
        'experiences' => 'fc-exp-itinerary',
        'on-a-budget' => 'fc-budget-grid',
        'luxury-escapes' => 'fc-lux-lookbook',
        'global-chapters' => 'fc-global-route',
        'not-on-the-atlas' => 'fc-atlas-scrapbook',
        'vineyard-tales' => 'fc-vine-flight',
        'coffee-classics' => 'fc-coffee-shelf',
    ];

    foreach ($markers as $slug => $marker) {
        seedFeatureCategoryArticle($slug);

        $this->get(route('features.show', $slug))
            ->assertSuccessful()
            ->assertSee($marker, false)
            ->assertDontSee('features-category-hero', false);
    }
});

test('unknown feature category returns not found', function () {
    $this->get('/features/not-a-real-chapter')->assertNotFound();
});

test('most feature category pages truncate article excerpts to 80 characters', function (string $slug) {
    $article = seedFeatureCategoryArticle($slug);
    $excerpt = Str::of($article->introduction)->stripTags()->squish()->toString();

    $response = $this->get(route('features.show', $slug));

    $response->assertSee(Str::limit($excerpt, 80), false);
    $response->assertDontSee($excerpt, false);
})->with(['art-culture', 'experiences', 'on-a-budget', 'vineyard-tales']);

test('the long-form feature category pages allow excerpts up to 500 characters', function (string $slug) {
    $article = seedFeatureCategoryArticle($slug);
    $excerpt = Str::of($article->introduction)->stripTags()->squish()->toString();

    $this->get(route('features.show', $slug))
        ->assertSee(Str::limit($excerpt, 500), false);
})->with(['luxury-escapes', 'global-chapters', 'not-on-the-atlas', 'coffee-classics']);
