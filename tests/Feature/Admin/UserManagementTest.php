<?php

use App\Models\Role;
use App\Models\User;

beforeEach(fn () => seedRbac());

test('super admin can create a sub admin with multiple roles', function () {
    $superAdmin = User::factory()->superAdmin()->create();

    $editorId = Role::query()->where('name', 'editor')->value('id');
    $viewerId = Role::query()->where('name', 'viewer')->value('id');

    $response = $this->actingAs($superAdmin)->post(route('admin.users.store'), [
        'name' => 'Sub Admin',
        'email' => 'subadmin@example.com',
        'roles' => [$editorId, $viewerId],
    ]);

    $response->assertRedirect(route('admin.users.index'));

    $subAdmin = User::query()->where('email', 'subadmin@example.com')->firstOrFail();

    expect($subAdmin->hasRole(['editor', 'viewer']))->toBeTrue();
    expect($subAdmin->hasPermission('view-dashboard'))->toBeTrue();
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

test('cannot remove super admin role from the last super admin', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    $editorId = Role::query()->where('name', 'editor')->value('id');

    $this->actingAs($superAdmin)
        ->from(route('admin.users.edit', $superAdmin))
        ->put(route('admin.users.update', $superAdmin), [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'roles' => [$editorId],
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('roles');

    expect($superAdmin->fresh()->isSuperAdmin())->toBeTrue();
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
