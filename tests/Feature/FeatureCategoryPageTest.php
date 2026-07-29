<?php

use App\Support\FeatureCatalog;

test('each feature category page returns a successful response', function (string $slug) {
    $category = FeatureCatalog::find($slug);

    expect($category)->not->toBeNull();

    $response = $this->get(route('features.show', $slug));

    $response->assertSuccessful();
    $response->assertSee(e($category['name']), false);
    $response->assertSee($category['lead'], false);
    $response->assertSee($category['tagline'], false);
    $response->assertSee($category['quote'], false);
    $response->assertSee($category['articles'][0]['title'], false);
    $response->assertSee('features-theme--'.$slug, false);
    $response->assertSee(asset($category['banner']), false);
    $response->assertSee('categoryBannerZoom', false);
    $response->assertSee('fc-pagination', false);
    $response->assertSee('javascript:void(0)', false);
    $response->assertSee('fc-siblings', false);
    $response->assertSee(route('features', absolute: false), false);
})->with(FeatureCatalog::slugs());

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
        $this->get(route('features.show', $slug))
            ->assertSuccessful()
            ->assertSee($marker, false)
            ->assertDontSee('features-category-hero', false);
    }
});

test('unknown feature category returns not found', function () {
    $this->get('/features/not-a-real-chapter')->assertNotFound();
});
