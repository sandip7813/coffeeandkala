<?php

test('the about page returns a successful response', function () {
    $response = $this->get(route('about'));

    $response->assertSuccessful();
    $response->assertSee('Our Story', false);
    $response->assertSee('Coffee &amp; Kala', false);
    $response->assertSee('images/about/banner.png', false);
    $response->assertSee('aboutBannerZoom', false);
    $response->assertSee('Show full banner image', false);
    $response->assertSee('about-banner-divider', false);
    $response->assertDontSee('aboutBannerLightbox', false);
    $response->assertSee('How it began', false);
    $response->assertSee('What we believe', false);
    $response->assertSee('The studio ritual', false);
    $response->assertSee('A circle of storytellers', false);
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
    $response->assertSee('about-topbar', false);
    $response->assertSee('about-reveal', false);
});

test('our story links to the about page from the home page', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee(route('about', absolute: false), false);
});
