<?php

use App\Models\HomeSectionMedia;
use App\Models\MediaFile;
use App\Models\User;

beforeEach(function () {
    seedRbac();
});

test('a super admin can add an active gallery image to the homepage Gallery carousel', function () {
    $user = User::factory()->superAdmin()->create();
    $media = MediaFile::factory()->ofType('gallery')->active()->create();

    $response = $this->actingAs($user)
        ->putJson(route('admin.gallery.home-section.toggle', $media));

    $response->assertOk()->assertJson(['active' => true]);
    $this->assertDatabaseHas('home_section_media', [
        'section' => HomeSectionMedia::SECTION_GALLERY,
        'media_id' => $media->id,
    ]);
});

test('toggling a second time removes the image from the homepage carousel', function () {
    $user = User::factory()->superAdmin()->create();
    $media = MediaFile::factory()->ofType('studio')->active()->create();
    HomeSectionMedia::create(['section' => HomeSectionMedia::SECTION_STUDIO, 'media_id' => $media->id, 'sort_order' => 0]);

    $response = $this->actingAs($user)
        ->putJson(route('admin.studio.home-section.toggle', $media));

    $response->assertOk()->assertJson(['active' => false]);
    $this->assertDatabaseMissing('home_section_media', ['media_id' => $media->id]);
});

test('a pending (not yet active) image cannot be added to the homepage carousel', function () {
    $user = User::factory()->superAdmin()->create();
    $media = MediaFile::factory()->ofType('gallery')->pending()->create();

    $this->actingAs($user)
        ->putJson(route('admin.gallery.home-section.toggle', $media))
        ->assertStatus(422);

    $this->assertDatabaseMissing('home_section_media', ['media_id' => $media->id]);
});

test('a user without manage-home-sections cannot toggle a media home section', function () {
    $user = userWithPermission('edit-gallery');
    $media = MediaFile::factory()->ofType('gallery')->active()->create();

    $this->actingAs($user)
        ->putJson(route('admin.gallery.home-section.toggle', $media))
        ->assertForbidden();
});

test('a studio image cannot be toggled through the gallery route', function () {
    $user = User::factory()->superAdmin()->create();
    $media = MediaFile::factory()->ofType('studio')->active()->create();

    $this->actingAs($user)
        ->putJson(route('admin.gallery.home-section.toggle', $media))
        ->assertNotFound();
});

test('the gallery list page shows the Home Page toggle only for users who can manage-home-sections', function () {
    $canManage = User::factory()->superAdmin()->create();
    $cannotManage = userWithPermission('view-gallery');
    MediaFile::factory()->ofType('gallery')->active()->create();

    $this->actingAs($canManage)
        ->get(route('admin.gallery.index'))
        ->assertSee('data-home-section-toggle', false);

    $this->actingAs($cannotManage)
        ->get(route('admin.gallery.index'))
        ->assertDontSee('data-home-section-toggle', false);
});

test('the gallery edit page shows the Home Page toggle only for users who can manage-home-sections', function () {
    $canManage = User::factory()->superAdmin()->create();
    $cannotManage = userWithPermission('edit-gallery');
    $media = MediaFile::factory()->ofType('gallery')->active()->create();

    $this->actingAs($canManage)
        ->get(route('admin.gallery.edit', $media))
        ->assertSee('data-home-section-toggle', false);

    $this->actingAs($cannotManage)
        ->get(route('admin.gallery.edit', $media))
        ->assertDontSee('data-home-section-toggle', false);
});

test('the gallery and studio caption fields are textareas', function () {
    $user = User::factory()->superAdmin()->create();
    $media = MediaFile::factory()->ofType('gallery')->active()->create();

    $this->actingAs($user)
        ->get(route('admin.gallery.create'))
        ->assertSee('<textarea', false)
        ->assertSee('name="caption"', false);

    $this->actingAs($user)
        ->get(route('admin.gallery.edit', $media))
        ->assertSee('<textarea', false)
        ->assertSee('name="caption"', false);
});

test('a caption longer than 255 characters is accepted', function () {
    $user = User::factory()->superAdmin()->create();
    $media = MediaFile::factory()->ofType('gallery')->active()->create();
    $longCaption = trim(str_repeat('A long caption sentence. ', 20));

    $this->actingAs($user)
        ->put(route('admin.gallery.update', $media), [
            'title' => $media->title,
            'caption' => $longCaption,
        ])
        ->assertRedirect(route('admin.gallery.index'));

    $this->assertDatabaseHas('media_files', ['id' => $media->id, 'caption' => $longCaption]);
});
