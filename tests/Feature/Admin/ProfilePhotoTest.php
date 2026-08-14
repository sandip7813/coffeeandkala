<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

test('guests cannot update a profile picture', function () {
    $this->put(route('admin.profile.photo.update'), [
        'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertRedirect(route('login'));
});

test('authenticated staff can upload a profile picture and a thumbnail is generated', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)
        ->put(route('admin.profile.photo.update'), [
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg', 400, 400),
        ])
        ->assertRedirect(route('admin.profile.edit'))
        ->assertSessionHas('status');

    $user->refresh();

    expect($user->profile_photo_path)->not->toBeNull();
    expect($user->profile_photo_thumbnail_path)->not->toBeNull();

    Storage::disk('public')->assertExists($user->profile_photo_path);
    Storage::disk('public')->assertExists($user->profile_photo_thumbnail_path);
});

test('uploading a new profile picture replaces the previous files', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)->put(route('admin.profile.photo.update'), [
        'profile_photo' => UploadedFile::fake()->image('first.jpg'),
    ]);

    $firstPath = $user->fresh()->profile_photo_path;
    $firstThumbnail = $user->fresh()->profile_photo_thumbnail_path;

    $this->actingAs($user)->put(route('admin.profile.photo.update'), [
        'profile_photo' => UploadedFile::fake()->image('second.jpg'),
    ]);

    $user->refresh();

    expect($user->profile_photo_path)->not->toBe($firstPath);
    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertMissing($firstThumbnail);
    Storage::disk('public')->assertExists($user->profile_photo_path);
});

test('profile picture upload rejects disallowed formats', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->put(route('admin.profile.photo.update'), [
            'profile_photo' => UploadedFile::fake()->create('avatar.pdf', 100, 'application/pdf'),
        ])
        ->assertSessionHasErrors('profile_photo');
});

test('profile picture upload rejects files over the configured max size', function () {
    $user = User::factory()->editor()->create();
    $maxKb = config('media.profile_photo.max_size_kb');

    $this->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->put(route('admin.profile.photo.update'), [
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg')->size($maxKb + 100),
        ])
        ->assertSessionHasErrors('profile_photo');
});

test('profile picture upload is required when submitting the form', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->put(route('admin.profile.photo.update'), [])
        ->assertSessionHasErrors('profile_photo');
});
