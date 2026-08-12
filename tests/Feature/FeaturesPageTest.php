<?php

test('the features page returns a successful response', function () {
    $response = $this->get(route('features'));

    $response->assertSuccessful();
    $response->assertDontSee('images/features/banner.png', false);
    $response->assertDontSee('featuresBannerZoom', false);
    $response->assertDontSee('features-banner-divider-mark', false);
    $response->assertSee('features-masthead', false);
    $response->assertSee('features-edition', false);
    $response->assertSee('Article of the day', false);
    $response->assertSee('data-journal-feature', false);
    $response->assertSee('data-home-carousel', false);
    $response->assertSee('The Quiet Architecture of Old Courtyards', false);
    $response->assertSee('The Village That Marks Time by Harvest', false);
    $response->assertSee('Brewing Between Pages on a Rainy Desk', false);
    $response->assertSee(route('features.show', 'art-culture', absolute: false), false);
    $response->assertSee(route('features.show', 'coffee-classics', absolute: false), false);
    $response->assertSee(route('features.show', 'not-on-the-atlas', absolute: false), false);
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
    expect($html)->toContain(
        '<a href="'.route('features.show', 'not-on-the-atlas').'">Not On The Atlas</a>'
    );

    // The sidebar's static pullquote is gone, replaced by a real leftover article.
    $response->assertSee('features-spotlight', false);
    $response->assertDontSee('features-pullquote', false);

    $response->assertDontSee('features-pagination', false);
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
