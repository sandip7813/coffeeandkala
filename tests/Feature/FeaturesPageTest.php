<?php

test('the features page returns a successful response', function () {
    $response = $this->get(route('features'));

    $response->assertSuccessful();
    $response->assertSee('images/features/banner.png', false);
    $response->assertSee('featuresBannerZoom', false);
    $response->assertSee('features-banner-divider-mark', false);
    $response->assertSee('features-mosaic', false);
    $response->assertSee('features-mosaic-grid', false);
    $response->assertSee('features-tile', false);
    $response->assertSee('features-tile-icon', false);
    $response->assertSee('features-tile-title', false);
    $response->assertSee('features-tile-lead', false);
    $response->assertSee('features-tile-image', false);
    $response->assertSee('features-tile-media', false);
    $response->assertSee('fa-landmark', false);
    $response->assertSee('fa-mug-hot', false);
    $response->assertSee('Choose a door', false);
    $response->assertSee('Art &amp; Culture', false);
    $response->assertSee('Coffee &amp; Classics', false);
    $response->assertSee(route('features.show', 'art-culture', absolute: false), false);
    $response->assertSee(route('features.show', 'coffee-classics', absolute: false), false);
    $response->assertSee('features-closing', false);
    $response->assertSee('Keep wandering', false);
    $html = $response->getContent();
    expect(
        str_contains($html, 'resources/css/features.css') || str_contains($html, 'build/assets/features-')
    )->toBeTrue();
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
});

test('features links from the header and footer navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('features', absolute: false), false);
    $home->assertSee(route('features.show', 'art-culture', absolute: false), false);
    $home->assertSee('>Articles</h3>', false);
});
