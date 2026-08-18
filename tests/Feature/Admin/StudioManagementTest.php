<?php

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

test('super admin can view the studio list', function () {
    $user = User::factory()->superAdmin()->create();
    $media = MediaFile::factory()->ofType('studio')->create(['title' => 'Ink & Ember']);

    $this->actingAs($user)
        ->get(route('admin.studio.index'))
        ->assertOk()
        ->assertSee($media->title);
});

test('a non-super-admin upload to studio is pending and requires approval', function () {
    $user = userWithPermission(['view-studio', 'upload-studio']);

    $response = $this->actingAs($user)->post(route('admin.studio.store'), [
        'title' => 'Desk Study',
        'caption' => 'A desk mid-thought.',
        'image' => UploadedFile::fake()->image('desk.jpg', 1200, 900),
    ]);

    $response->assertRedirect(route('admin.studio.index'));

    $media = MediaFile::where('title', 'Desk Study')->firstOrFail();

    expect($media->type)->toBe('studio');
    expect($media->status)->toBe('pending');

    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->put(route('admin.studio.approve', $media))
        ->assertRedirect(route('admin.studio.index'));

    expect($media->refresh()->status)->toBe('active');
});

test('a user with approve-studio (but not super admin) can approve a pending upload', function () {
    $approver = userWithPermission(['view-studio', 'approve-studio']);
    $media = MediaFile::factory()->ofType('studio')->pending()->create();

    $this->actingAs($approver)
        ->put(route('admin.studio.approve', $media))
        ->assertRedirect(route('admin.studio.index'));

    expect($media->refresh()->status)->toBe('active');
});

test('a user without approve-studio cannot approve a pending upload', function () {
    $user = userWithPermission(['view-studio', 'upload-studio']);
    $media = MediaFile::factory()->ofType('studio')->pending()->create();

    $this->actingAs($user)
        ->put(route('admin.studio.approve', $media))
        ->assertForbidden();
});

test('a user with delete-studio can delete a studio image', function () {
    $user = userWithPermission(['view-studio', 'delete-studio']);
    $media = MediaFile::factory()->ofType('studio')->create();

    $this->actingAs($user)
        ->delete(route('admin.studio.destroy', $media))
        ->assertRedirect(route('admin.studio.index'));

    $this->assertDatabaseMissing('media_files', ['id' => $media->id]);
});

test('a studio url cannot manage a gallery media file', function () {
    $user = userWithPermission(['view-studio', 'edit-studio']);
    $galleryMedia = MediaFile::factory()->ofType('gallery')->create();

    $this->actingAs($user)
        ->get(route('admin.studio.edit', $galleryMedia))
        ->assertNotFound();
});
