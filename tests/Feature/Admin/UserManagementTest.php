<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(fn () => seedRbac());

test('cannot create a user with the super admin role', function () {
    Mail::fake();

    $superAdmin = User::factory()->superAdmin()->create();
    $superAdminRoleId = Role::query()->where('name', 'super_admin')->value('id');

    $this->actingAs($superAdmin)
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), [
            'first_name' => 'Another',
            'last_name' => 'Super',
            'email' => 'another-super@example.com',
            'role' => $superAdminRoleId,
        ])
        ->assertRedirect(route('admin.users.create'))
        ->assertSessionHasErrors('role');

    expect(User::query()->where('email', 'another-super@example.com')->exists())->toBeFalse();
});

test('super admin can create an admin user', function () {
    Mail::fake();

    $superAdmin = User::factory()->superAdmin()->create();
    $adminId = Role::query()->where('name', 'admin')->value('id');

    $this->actingAs($superAdmin)->post(route('admin.users.store'), [
        'first_name' => 'Site',
        'last_name' => 'Admin',
        'email' => 'site-admin@example.com',
        'role' => $adminId,
    ])->assertRedirect(route('admin.users.index'));

    $user = User::query()->where('email', 'site-admin@example.com')->firstOrFail();

    expect($user->hasRole('admin'))->toBeTrue()
        ->and($user->hasRole('super_admin'))->toBeFalse();
});

test('create user form does not offer the super admin role', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.users.create'))
        ->assertSuccessful()
        ->assertDontSee('value="'.Role::query()->where('name', 'super_admin')->value('id').'"', false)
        ->assertSee('Admin', false)
        ->assertSee('data-page-loading="Creating user…"', false);
});

test('cannot delete own account even as super admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $this->actingAs($superAdmin)
        ->from(route('admin.users.index'))
        ->delete(route('admin.users.destroy', $superAdmin))
        ->assertRedirect()
        ->assertSessionHasErrors('user');

    $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);
});

test('cannot delete the last remaining super admin', function () {
    $keeper = User::factory()->superAdmin()->create();
    $other = User::factory()->superAdmin()->create();

    $this->actingAs($keeper)
        ->delete(route('admin.users.destroy', $other))
        ->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseMissing('users', ['id' => $other->id]);

    expect(
        User::query()->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))->count()
    )->toBe(1);
});

test('cannot change a super admin to another role', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $editorId = Role::query()->where('name', 'editor')->value('id');

    $this->actingAs($superAdmin)
        ->from(route('admin.users.edit', $superAdmin))
        ->put(route('admin.users.update', $superAdmin), [
            'first_name' => $superAdmin->first_name,
            'last_name' => $superAdmin->last_name,
            'email' => $superAdmin->email,
            'role' => $editorId,
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('role');

    expect($superAdmin->fresh()->isSuperAdmin())->toBeTrue();
});

test('users index exposes the branded delete loader asset', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    expect(is_file(public_path('logo-spinner.gif')))->toBeTrue();

    $this->actingAs($superAdmin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertSee('data-page-loader-src="'.asset('logo-spinner.gif').'"', false);
});

test('edit user form shows a saving page loader attribute', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $user = User::factory()->editor()->create();

    $this->actingAs($superAdmin)
        ->get(route('admin.users.edit', $user))
        ->assertSuccessful()
        ->assertSee('data-page-loading="Saving user…"', false);
});

test('super admin role cannot be deleted', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $role = Role::query()->where('name', 'super_admin')->firstOrFail();

    $this->actingAs($superAdmin)
        ->from(route('admin.roles.index'))
        ->delete(route('admin.roles.destroy', $role))
        ->assertRedirect()
        ->assertSessionHasErrors('role');

    $this->assertDatabaseHas('adminlte_roles', ['name' => 'super_admin']);
});
