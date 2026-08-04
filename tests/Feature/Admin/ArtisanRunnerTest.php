<?php

use App\Models\User;
use App\Support\ArtisanCatalog;
use Illuminate\Validation\ValidationException;

beforeEach(fn () => seedRbac());

test('guests cannot view the artisan runner', function () {
    $this->get(route('admin.artisan.index'))
        ->assertRedirect(route('login'));
});

test('editors without manage-settings cannot view the artisan runner', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)
        ->get(route('admin.artisan.index'))
        ->assertForbidden();
});

test('admins see the restricted gate until they unlock with their password', function () {
    $user = User::factory()->admin()->create([
        'password' => 'secret-password',
    ]);

    $this->actingAs($user)
        ->get(route('admin.artisan.index'))
        ->assertSuccessful()
        ->assertSee('Restricted page', false)
        ->assertDontSee('Quick actions', false);

    $this->actingAs($user)
        ->from(route('admin.artisan.index'))
        ->post(route('admin.artisan.unlock'), [
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('admin.artisan.index'))
        ->assertSessionHasErrors('password');

    $this->actingAs($user)
        ->post(route('admin.artisan.unlock'), [
            'password' => 'secret-password',
        ])
        ->assertRedirect(route('admin.artisan.index'))
        ->assertSessionHas('status')
        ->assertSessionHas('artisan_runner.confirmed_at');

    $this->actingAs($user)
        ->get(route('admin.artisan.index'))
        ->assertSuccessful()
        ->assertSee('Artisan Runner', false)
        ->assertSee('Migrations', false)
        ->assertSee('Run migrations', false)
        ->assertSee('Custom command', false)
        ->assertDontSee('migrate:rollback', false)
        ->assertDontSee('Confirm — run this command', false);
});

test('locked sessions cannot run artisan commands', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->post(route('admin.artisan.run'), [
            'preset' => 'migrate-status',
            'confirm' => '1',
        ])
        ->assertForbidden();
});

test('every artisan run requires confirmation', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->withSession(unlockedArtisanSession())
        ->from(route('admin.artisan.index'))
        ->post(route('admin.artisan.run'), [
            'preset' => 'migrate-status',
        ])
        ->assertRedirect(route('admin.artisan.index'))
        ->assertSessionHasErrors('confirm');
});

test('admins can run an allowlisted artisan preset after confirming', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->withSession(unlockedArtisanSession())
        ->post(route('admin.artisan.run'), [
            'preset' => 'migrate-status',
            'confirm' => '1',
        ])
        ->assertRedirect(route('admin.artisan.index'))
        ->assertSessionHas('status')
        ->assertSessionHas('artisan_result.succeeded', true)
        ->assertSessionHas('artisan_result.command', 'php artisan migrate:status');
});

test('admins can run a custom allowlisted command string', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->withSession(unlockedArtisanSession())
        ->post(route('admin.artisan.run'), [
            'command' => 'about',
            'confirm' => '1',
        ])
        ->assertRedirect(route('admin.artisan.index'))
        ->assertSessionHas('artisan_result.succeeded', true)
        ->assertSessionHas('artisan_result.command', 'php artisan about');
});

test('admins can seed a specific seeder via preset', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->withSession(unlockedArtisanSession())
        ->post(route('admin.artisan.run'), [
            'preset' => 'db-seed',
            'seeder' => 'AdminLteRbacSeeder',
            'confirm' => '1',
        ])
        ->assertRedirect(route('admin.artisan.index'))
        ->assertSessionHas('artisan_result.succeeded', true);
});

test('disallowed commands are rejected', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->withSession(unlockedArtisanSession())
        ->from(route('admin.artisan.index'))
        ->post(route('admin.artisan.run'), [
            'command' => 'tinker',
            'confirm' => '1',
        ])
        ->assertRedirect(route('admin.artisan.index'))
        ->assertSessionHasErrors('command');
});

test('migrate rollback is not allowed', function () {
    $user = User::factory()->admin()->create();

    $this->actingAs($user)
        ->withSession(unlockedArtisanSession())
        ->from(route('admin.artisan.index'))
        ->post(route('admin.artisan.run'), [
            'command' => 'migrate:rollback',
            'confirm' => '1',
        ])
        ->assertRedirect(route('admin.artisan.index'))
        ->assertSessionHasErrors('command');
});

test('shell metacharacters are rejected', function () {
    expect(fn () => ArtisanCatalog::parse('cache:clear; rm -rf /'))
        ->toThrow(ValidationException::class);
});

test('artisan catalog groups migration presets together', function () {
    $groups = ArtisanCatalog::groupedPresets();

    expect($groups)->toHaveKey('migrations')
        ->and(collect($groups['migrations']['presets'])->pluck('command')->all())
        ->toContain('migrate', 'migrate:status', 'migrate:install')
        ->not->toContain('migrate:rollback');
});

test('artisan catalog lists application seeders', function () {
    expect(ArtisanCatalog::seeders())
        ->toContain('DatabaseSeeder')
        ->toContain('AdminLteRbacSeeder')
        ->toContain('SuperAdminSeeder');
});
