<?php

test('the studio page returns a successful response', function () {
    $response = $this->get(route('studio'));

    $response->assertSuccessful();
    $response->assertSee('Gallery of visual poetry', false);
    $response->assertSee('images/studio/banner.png', false);
    $response->assertSee('studioBannerZoom', false);
    $response->assertSee('studio-banner-divider-mark', false);
    $response->assertSee('Where colour finds', false);
    $response->assertSee('studio-intro', false);
    $response->assertSee('works on the wall', false);
    $response->assertSee('galleryLightRoot', false);
    $response->assertSee('studio-wall-surface', false);
    $response->assertSee('studio-photo-frame', false);
    $response->assertSee('studio-photo-frame-edge', false);
    $response->assertSee('studio-photo-frame-corners', false);
    $response->assertSee('studio-photo-rabbet', false);
    $response->assertSee('studio-photo-mat', false);
    $response->assertSee('data-galleria-index', false);
    $response->assertSee('galleriaModal', false);
    $response->assertSee('galleria-modal--studio', false);
    $response->assertSee('galleriaInstance', false);
    $response->assertSee('galleryPlatesData', false);
    $html = $response->getContent();
    expect(
        str_contains($html, 'resources/css/studio.css') || str_contains($html, 'build/assets/studio-')
    )->toBeTrue();
    expect(
        str_contains($html, 'resources/js/gallery.js') || str_contains($html, 'build/assets/gallery-')
    )->toBeTrue();
    $response->assertSee('Ember Fields', false);
    $response->assertSee('Acrylic on canvas', false);
    $response->assertSee('galleria/1.6.1', false);
    $response->assertDontSee('gallery-poster', false);
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
});

test('studio links from the header and home navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('studio', absolute: false), false);
});
