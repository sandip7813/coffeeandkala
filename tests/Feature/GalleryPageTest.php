<?php

test('the gallery page returns a successful response', function () {
    $response = $this->get(route('gallery'));

    $response->assertSuccessful();
    $response->assertSee('Gallery of Visual Storytelling', false);
    $response->assertSee('images/gallery/banner.png', false);
    $response->assertSee('galleryBannerZoom', false);
    $response->assertSee('gallery-banner-divider-mark', false);
    $response->assertSee('Frames that refuse', false);
    $response->assertSee('gallery-intro', false);
    $response->assertSee('plates on the wall', false);
    $response->assertSee('galleryLightRoot', false);
    $response->assertSee('gallery-wall-surface', false);
    $response->assertSee('gallery-poster', false);
    $response->assertSee('data-galleria-index', false);
    $response->assertSee('galleriaModal', false);
    $response->assertSee('galleriaInstance', false);
    $response->assertSee('galleryPlatesData', false);
    $html = $response->getContent();
    expect(
        str_contains($html, 'resources/css/gallery.css') || str_contains($html, 'build/assets/gallery-')
    )->toBeTrue();
    expect(
        str_contains($html, 'resources/js/gallery.js') || str_contains($html, 'build/assets/gallery-')
    )->toBeTrue();
    $response->assertSee('The Last Light Over the Northern Rail Corridor at Dusk', false);
    $response->assertSee('gallery-poster-title', false);
    $response->assertSee('w=400', false);
    $response->assertSee('galleria/1.6.1', false);
    $response->assertDontSee('data-fancybox', false);
    $response->assertDontSee('@fancyapps/ui', false);
    $response->assertDontSee('lightgallery', false);
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
});

test('gallery links from the header and home navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('gallery', absolute: false), false);
});
