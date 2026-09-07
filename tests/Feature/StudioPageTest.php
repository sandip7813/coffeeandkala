<?php

use App\Models\MediaFile;
use Illuminate\Support\Str;

test('the studio page returns a successful response', function () {
    $work = MediaFile::factory()->ofType(MediaFile::TYPE_STUDIO)->active()->create(['title' => 'A Real Studio Work']);

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
    $response->assertSee($work->title, false);
    $response->assertSee('galleria/1.6.1', false);
    $response->assertDontSee('gallery-poster', false);
    $response->assertDontSee('editorialSidebar', false);
    $response->assertDontSee('toggleLeftDrawer', false);
});

test('only active studio images appear on the studio page', function () {
    $active = MediaFile::factory()->ofType(MediaFile::TYPE_STUDIO)->active()->create(['title' => 'An Active Work']);
    $pending = MediaFile::factory()->ofType(MediaFile::TYPE_STUDIO)->pending()->create(['title' => 'A Pending Work']);
    $inactive = MediaFile::factory()->ofType(MediaFile::TYPE_STUDIO)->inactive()->create(['title' => 'An Inactive Work']);
    $gallery = MediaFile::factory()->ofType(MediaFile::TYPE_GALLERY)->active()->create(['title' => 'A Gallery Plate']);

    $response = $this->get(route('studio'));

    $response->assertSuccessful();
    $response->assertSee($active->title, false);
    $response->assertDontSee($pending->title, false);
    $response->assertDontSee($inactive->title, false);
    $response->assertDontSee($gallery->title, false);
});

test('a studio work title longer than 50 characters is trimmed', function () {
    $longTitle = str_repeat('A very long studio work title indeed. ', 3);
    MediaFile::factory()->ofType(MediaFile::TYPE_STUDIO)->active()->create(['title' => $longTitle]);

    $response = $this->get(route('studio'));

    $response->assertSuccessful();
    $response->assertDontSee($longTitle, false);
    $response->assertSee(Str::limit($longTitle, 50), false);
});

test('studio links from the header and home navigation', function () {
    $home = $this->get(route('home'));

    $home->assertSuccessful();
    $home->assertSee(route('studio', absolute: false), false);
});
