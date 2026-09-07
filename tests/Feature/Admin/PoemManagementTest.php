<?php

use App\Models\Poem;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    seedRbac();
    Storage::fake('public');
});

test('super admin can view the poetry list', function () {
    $user = User::factory()->superAdmin()->create();
    $poem = Poem::factory()->create(['title' => 'The Weight of Rain']);

    $this->actingAs($user)
        ->get(route('admin.poetry.index'))
        ->assertOk()
        ->assertSee($poem->title);
});

test('a user without view-poetry cannot view the poetry list', function () {
    $user = userWithPermission('view-gallery');

    $this->actingAs($user)
        ->get(route('admin.poetry.index'))
        ->assertForbidden();
});

test('super admin can add a poem, active immediately and auto-approved, with a featured image', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->post(route('admin.poetry.store'), [
        'title' => 'The Weight of Rain',
        'body' => "It rained the day you left,\nand something in the gutters has been grieving ever since.",
        'featured_image' => UploadedFile::fake()->image('rain.jpg', 1200, 900),
    ]);

    $response->assertRedirect(route('admin.poetry.index'));

    $poem = Poem::where('title', 'The Weight of Rain')->firstOrFail();

    expect($poem->slug)->toBe('the-weight-of-rain');
    expect($poem->status)->toBe('active');
    expect($poem->created_by)->toBe($user->id);
    expect($poem->approved_by)->toBe($user->id);
    expect($poem->approved_at)->not->toBeNull();
    expect($poem->body)->toContain('grieving ever since');

    $featuredImage = $poem->featuredImage()->firstOrFail();
    expect($featuredImage->role)->toBe('featured');
    Storage::disk('public')->assertExists($featuredImage->thumbnail_path);
    Storage::disk('public')->assertExists($featuredImage->large_path);
});

test('a non-super-admin poem is pending and requires approval', function () {
    $user = userWithPermission(['view-poetry', 'upload-poetry']);

    $response = $this->actingAs($user)->post(route('admin.poetry.store'), [
        'title' => 'Things We Left Unspoken',
        'body' => 'Some words are not meant to be spoken.',
        'featured_image' => UploadedFile::fake()->image('drawer.jpg'),
    ]);

    $response->assertRedirect(route('admin.poetry.index'));

    $poem = Poem::where('title', 'Things We Left Unspoken')->firstOrFail();
    expect($poem->status)->toBe('pending');
    expect($poem->approved_by)->toBeNull();
});

test('a user without upload-poetry cannot add a poem', function () {
    $user = userWithPermission('view-poetry');

    $this->actingAs($user)
        ->post(route('admin.poetry.store'), [
            'title' => 'X',
            'body' => 'Y',
            'featured_image' => UploadedFile::fake()->image('x.jpg'),
        ])
        ->assertForbidden();
});

test('featured image is required to add a poem', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->post(route('admin.poetry.store'), ['title' => 'X', 'body' => 'Y'])
        ->assertSessionHasErrors('featured_image');
});

test('a user with edit-poetry can update title and body without replacing the image', function () {
    $user = userWithPermission(['view-poetry', 'edit-poetry']);
    $poem = Poem::factory()->create(['title' => 'Old Title', 'body' => 'Old body.']);

    $this->actingAs($user)
        ->put(route('admin.poetry.update', $poem), [
            'title' => 'New Title',
            'body' => 'New body.',
        ])
        ->assertRedirect(route('admin.poetry.index'));

    $this->assertDatabaseHas('poems', [
        'id' => $poem->id,
        'title' => 'New Title',
        'body' => 'New body.',
    ]);
});

test('replacing the featured image on update deletes the old files', function () {
    $user = User::factory()->superAdmin()->create();
    $poem = Poem::factory()->create();
    $this->actingAs($user)->post(route('admin.poetry.store'), [
        'title' => $poem->title.' 2',
        'body' => 'Body.',
        'featured_image' => UploadedFile::fake()->image('first.jpg'),
    ]);
    $created = Poem::where('title', $poem->title.' 2')->firstOrFail();
    $originalImage = $created->featuredImage()->firstOrFail();
    $oldPath = $originalImage->large_path;

    $this->actingAs($user)->put(route('admin.poetry.update', $created), [
        'title' => $created->title,
        'body' => $created->body,
        'featured_image' => UploadedFile::fake()->image('second.jpg'),
    ]);

    Storage::disk('public')->assertMissing($oldPath);
    expect($created->featuredImage()->first()->large_path)->not->toBe($oldPath);
});

test('a user without edit-poetry cannot update a poem', function () {
    $user = userWithPermission('view-poetry');
    $poem = Poem::factory()->create();

    $this->actingAs($user)
        ->put(route('admin.poetry.update', $poem), ['title' => 'X', 'body' => 'Y'])
        ->assertForbidden();
});

test('a user with change-poetry-status can toggle active/inactive', function () {
    $user = userWithPermission(['view-poetry', 'change-poetry-status']);
    $poem = Poem::factory()->active()->create();

    $this->actingAs($user)
        ->put(route('admin.poetry.status.update', $poem))
        ->assertRedirect(route('admin.poetry.index'));

    expect($poem->fresh()->status)->toBe('inactive');
});

test('a pending poem cannot have its status toggled', function () {
    $user = userWithPermission(['view-poetry', 'change-poetry-status']);
    $poem = Poem::factory()->pending()->create();

    $this->actingAs($user)
        ->put(route('admin.poetry.status.update', $poem))
        ->assertStatus(422);
});

test('a user with approve-poetry can approve a pending poem', function () {
    $user = userWithPermission(['view-poetry', 'approve-poetry']);
    $poem = Poem::factory()->pending()->create();

    $this->actingAs($user)
        ->put(route('admin.poetry.approve', $poem))
        ->assertRedirect(route('admin.poetry.index'));

    $poem->refresh();
    expect($poem->status)->toBe('active');
    expect($poem->approved_by)->toBe($user->id);
});

test('a user with delete-poetry can delete a poem and its featured image files', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user)->post(route('admin.poetry.store'), [
        'title' => 'To Delete',
        'body' => 'Body.',
        'featured_image' => UploadedFile::fake()->image('delete-me.jpg'),
    ]);
    $poem = Poem::where('title', 'To Delete')->firstOrFail();
    $image = $poem->featuredImage()->firstOrFail();
    $path = $image->large_path;

    $this->actingAs($user)
        ->delete(route('admin.poetry.destroy', $poem))
        ->assertRedirect(route('admin.poetry.index'));

    $this->assertDatabaseMissing('poems', ['id' => $poem->id]);
    Storage::disk('public')->assertMissing($path);
});

test('the poetry list page opens a featured image thumbnail in a fancybox lightbox', function () {
    $user = User::factory()->superAdmin()->create();
    $this->actingAs($user)->post(route('admin.poetry.store'), [
        'title' => 'A Lightbox Poem',
        'body' => 'Body.',
        'featured_image' => UploadedFile::fake()->image('lightbox.jpg'),
    ]);
    $poem = Poem::where('title', 'A Lightbox Poem')->firstOrFail();
    $image = $poem->featuredImage()->firstOrFail();

    $response = $this->actingAs($user)->get(route('admin.poetry.index'));

    $response->assertOk();
    $response->assertSee('data-fancybox="poetry"', false);
    $response->assertSee('href="'.$image->large_url.'"', false);
});

test('the poetry search filters by title and status', function () {
    $user = User::factory()->superAdmin()->create();
    $match = Poem::factory()->active()->create(['title' => 'Findable Poem']);
    Poem::factory()->active()->create(['title' => 'Other Poem']);

    $response = $this->actingAs($user)->get(route('admin.poetry.index', ['title' => 'Findable']));

    $response->assertOk();
    $response->assertSee($match->title);
    $response->assertDontSee('Other Poem');
});
