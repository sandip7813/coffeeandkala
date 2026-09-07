<?php

use App\Models\MediaFile;
use Illuminate\Support\Str;

test('the gallery page returns a successful response', function () {
    $plate = MediaFile::factory()->ofType(MediaFile::TYPE_GALLERY)->active()->create(['title' => 'A Real Gallery Plate']);

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
    $response->assertSee($plate->title, false);
    $response->assertSee($plate->thumbnail_url, false);
    $response->assertSee('gallery-poster-title', false);
    $response->assertSee('galleria/1.6.1', false);
    $response->assertDontSee('data-fancybox', false);
    $response->assertDontSee('@fancyapps/ui', false);
    $response->assertDontSee('lightgallery', false);
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
});

test('only active gallery images appear on the gallery page', function () {
    $active = MediaFile::factory()->ofType(MediaFile::TYPE_GALLERY)->active()->create(['title' => 'An Active Plate']);
    $pending = MediaFile::factory()->ofType(MediaFile::TYPE_GALLERY)->pending()->create(['title' => 'A Pending Plate']);
    $inactive = MediaFile::factory()->ofType(MediaFile::TYPE_GALLERY)->inactive()->create(['title' => 'An Inactive Plate']);
    $studio = MediaFile::factory()->ofType(MediaFile::TYPE_STUDIO)->active()->create(['title' => 'A Studio Work']);

    $response = $this->get(route('gallery'));

    $response->assertSuccessful();
    $response->assertSee($active->title, false);
    $response->assertDontSee($pending->title, false);
    $response->assertDontSee($inactive->title, false);
    $response->assertDontSee($studio->title, false);
});

test('a gallery plate title longer than 50 characters is trimmed', function () {
    $longTitle = str_repeat('A very long gallery plate title indeed. ', 3);
    MediaFile::factory()->ofType(MediaFile::TYPE_GALLERY)->active()->create(['title' => $longTitle]);

    $response = $this->get(route('gallery'));

    $response->assertSuccessful();
    $response->assertDontSee($longTitle, false);
    $response->assertSee(Str::limit($longTitle, 50), false);
});

test('gallery links from the header and home navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('gallery', absolute: false), false);
});
