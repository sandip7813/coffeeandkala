<?php

test('the gallery page returns a successful response', function () {
    $response = $this->get(route('gallery'));

    $response->assertSuccessful();
    $response->assertSee('Gallery of the Visual Storytelling', false);
    $response->assertSee('images/gallery/hero.png', false);
    $response->assertSee('gallery-banner-title', false);
    $response->assertSee('This isn’t merely a gallery', false);
    $response->assertSee('data-gallery-intro-toggle', false);
    $response->assertSee('Know More', false);
    $response->assertSee('gallery-intro', false);
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
