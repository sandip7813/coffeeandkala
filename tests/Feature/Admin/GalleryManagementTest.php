<?php

use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

test('super admin can view the gallery list', function () {
    $user = User::factory()->superAdmin()->create();
    $media = MediaFile::factory()->ofType('gallery')->create(['title' => 'Morning Ritual']);

    $this->actingAs($user)
        ->get(route('admin.gallery.index'))
        ->assertOk()
        ->assertSee($media->title);
});

test('a user without view-gallery cannot view the gallery list', function () {
    $user = userWithPermission('view-studio');

    $this->actingAs($user)
        ->get(route('admin.gallery.index'))
        ->assertForbidden();
});

test('super admin upload is active immediately and auto-approved', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->post(route('admin.gallery.store'), [
        'title' => 'Lone Oar',
        'caption' => 'Mist on still water.',
        'image' => UploadedFile::fake()->image('boat.jpg', 1200, 900),
    ]);

    $response->assertRedirect(route('admin.gallery.index'));

    $media = MediaFile::where('title', 'Lone Oar')->firstOrFail();

    expect($media->type)->toBe('gallery');
    expect($media->status)->toBe('active');
    expect($media->uploaded_by)->toBe($user->id);
    expect($media->approved_by)->toBe($user->id);
    expect($media->approved_at)->not->toBeNull();

    Storage::disk('public')->assertExists($media->thumbnail_path);
    Storage::disk('public')->assertExists($media->large_path);
    Storage::disk('public')->assertExists($media->original_path);
});

test('a non-super-admin upload is pending and requires approval', function () {
    $user = userWithPermission(['view-gallery', 'upload-gallery']);

    $response = $this->actingAs($user)->post(route('admin.gallery.store'), [
        'title' => 'Crimson Passage',
        'caption' => 'Prayer flags and stone walls.',
        'image' => UploadedFile::fake()->image('temple.jpg', 1200, 900),
    ]);

    $response->assertRedirect(route('admin.gallery.index'));

    $media = MediaFile::where('title', 'Crimson Passage')->firstOrFail();

    expect($media->status)->toBe('pending');
    expect($media->approved_by)->toBeNull();
    expect($media->approved_at)->toBeNull();
});

test('the upload form validates title, caption, and image', function () {
    $user = userWithPermission(['view-gallery', 'upload-gallery']);

    $this->actingAs($user)
        ->post(route('admin.gallery.store'), [])
        ->assertSessionHasErrors(['title', 'caption', 'image']);
});

test('a user without upload-gallery cannot upload', function () {
    $user = userWithPermission('view-gallery');

    $this->actingAs($user)
        ->post(route('admin.gallery.store'), [
            'title' => 'Denied',
            'caption' => 'Should not save.',
            'image' => UploadedFile::fake()->image('denied.jpg'),
        ])
        ->assertForbidden();
});

test('a user without approve-gallery cannot approve a pending upload', function () {
    $uploader = userWithPermission(['view-gallery', 'upload-gallery']);
    $media = MediaFile::factory()->ofType('gallery')->pending()->create(['uploaded_by' => $uploader->id]);

    $this->actingAs($uploader)
        ->put(route('admin.gallery.approve', $media))
        ->assertForbidden();
});

test('a user with approve-gallery can approve a pending upload', function () {
    $approver = userWithPermission(['view-gallery', 'approve-gallery']);
    $media = MediaFile::factory()->ofType('gallery')->pending()->create();

    $this->actingAs($approver)
        ->put(route('admin.gallery.approve', $media))
        ->assertRedirect(route('admin.gallery.index'));

    $media->refresh();

    expect($media->status)->toBe('active');
    expect($media->approved_by)->toBe($approver->id);
    expect($media->approved_at)->not->toBeNull();
});

test('a super admin can approve a pending upload without an explicit permission', function () {
    $media = MediaFile::factory()->ofType('gallery')->pending()->create();
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->put(route('admin.gallery.approve', $media))
        ->assertRedirect(route('admin.gallery.index'));

    expect($media->refresh()->status)->toBe('active');
});

test('an upload by a user who already holds approve-gallery is active immediately', function () {
    $user = userWithPermission(['view-gallery', 'upload-gallery', 'approve-gallery']);

    $this->actingAs($user)->post(route('admin.gallery.store'), [
        'title' => 'Pre-approved',
        'caption' => 'Uploaded by an approver.',
        'image' => UploadedFile::fake()->image('preapproved.jpg', 1200, 900),
    ]);

    $media = MediaFile::where('title', 'Pre-approved')->firstOrFail();

    expect($media->status)->toBe('active');
    expect($media->approved_by)->toBe($user->id);
});

test('the sidebar shows a pending-approval badge only to users who can approve, but still links to the normal list', function () {
    MediaFile::factory()->ofType('gallery')->pending()->count(2)->create();

    $approver = userWithPermission(['view-gallery', 'approve-gallery']);
    $response = $this->actingAs($approver)->get(route('admin.dashboard'))->assertOk();
    $response->assertSee('2');
    $response->assertSee(route('admin.gallery.index'), false);
    $response->assertDontSee(route('admin.gallery.index', ['status' => 'pending']), false);

    $uploader = userWithPermission(['view-gallery', 'upload-gallery']);
    $response = $this->actingAs($uploader)->get(route('admin.dashboard'))->assertOk();
    $response->assertSee(route('admin.gallery.index'), false);
});

test('a user with edit-gallery can update title and caption', function () {
    $user = userWithPermission(['view-gallery', 'edit-gallery']);
    $media = MediaFile::factory()->ofType('gallery')->create(['title' => 'Old title', 'caption' => 'Old caption']);

    $this->actingAs($user)
        ->put(route('admin.gallery.update', $media), [
            'title' => 'New title',
            'caption' => 'New caption',
        ])
        ->assertRedirect(route('admin.gallery.index'));

    $this->assertDatabaseHas('media_files', [
        'id' => $media->id,
        'title' => 'New title',
        'caption' => 'New caption',
    ]);
});

test('a user with change-gallery-status can toggle active/inactive', function () {
    $user = userWithPermission(['view-gallery', 'change-gallery-status']);
    $media = MediaFile::factory()->ofType('gallery')->active()->create();

    $this->actingAs($user)
        ->put(route('admin.gallery.status.update', $media))
        ->assertRedirect(route('admin.gallery.index'));

    expect($media->refresh()->status)->toBe('inactive');
});

test('a user with delete-gallery can delete an image and its files', function () {
    $user = userWithPermission(['view-gallery', 'delete-gallery']);
    $media = MediaFile::factory()->ofType('gallery')->create();

    Storage::disk('public')->put($media->thumbnail_path, 'x');
    Storage::disk('public')->put($media->large_path, 'x');
    Storage::disk('public')->put($media->original_path, 'x');

    $this->actingAs($user)
        ->delete(route('admin.gallery.destroy', $media))
        ->assertRedirect(route('admin.gallery.index'));

    $this->assertDatabaseMissing('media_files', ['id' => $media->id]);
    Storage::disk('public')->assertMissing($media->thumbnail_path);
    Storage::disk('public')->assertMissing($media->large_path);
    Storage::disk('public')->assertMissing($media->original_path);
});

test('a gallery url cannot manage a studio media file', function () {
    $user = userWithPermission(['view-gallery', 'edit-gallery', 'delete-gallery']);
    $studioMedia = MediaFile::factory()->ofType('studio')->create();

    $this->actingAs($user)
        ->get(route('admin.gallery.edit', $studioMedia))
        ->assertNotFound();
});
