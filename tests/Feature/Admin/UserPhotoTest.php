<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

test('guests cannot update another user\'s profile picture', function () {
    $user = User::factory()->editor()->create();

    $this->put(route('admin.users.photo.update', $user), [
        'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
    ])->assertRedirect(route('login'));
});

test('users without manage-users permission cannot update a profile picture', function () {
    $editor = User::factory()->editor()->create();
    $target = User::factory()->editor()->create();

    $this->actingAs($editor)
        ->put(route('admin.users.photo.update', $target), [
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg'),
        ])->assertForbidden();
});

test('an admin can upload a profile picture for another user', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $target = User::factory()->editor()->create();

    $this->actingAs($superAdmin)
        ->put(route('admin.users.photo.update', $target), [
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg', 400, 400),
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    $target->refresh();

    expect($target->profile_photo_path)->not->toBeNull();
    expect($target->profile_photo_thumbnail_path)->not->toBeNull();

    Storage::disk('public')->assertExists($target->profile_photo_path);
    Storage::disk('public')->assertExists($target->profile_photo_thumbnail_path);
});

test('users index shows a photo column with a placeholder icon when no picture is uploaded', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('Photo', false)
        ->assertSee('bi-person-circle', false);
});
