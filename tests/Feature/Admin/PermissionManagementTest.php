<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

beforeEach(fn () => seedRbac());

test('super admin can invent a new permission', function () {
    $user = User::factory()->superAdmin()->create();

    $response = $this->actingAs($user)->post(route('admin.permissions.store'), [
        'name' => 'manage-articles',
        'label' => 'Manage Articles',
    ]);

    $response->assertRedirect(route('admin.permissions.index'));

    $this->assertDatabaseHas('adminlte_permissions', [
        'name' => 'manage-articles',
        'label' => 'Manage Articles',
    ]);
});

test('invented permission can be assigned to a role', function () {
    $user = User::factory()->superAdmin()->create();

    $permission = Permission::create([
        'name' => 'manage-gallery',
        'label' => 'Manage Gallery',
    ]);

    $role = Role::query()->where('name', 'editor')->firstOrFail();

    $this->actingAs($user)->put(route('admin.roles.update', $role), [
        'name' => $role->name,
        'label' => $role->label,
        'permissions' => array_merge(
            $role->permissions->pluck('id')->all(),
            [$permission->id],
        ),
    ])->assertRedirect(route('admin.roles.index'));

    expect($role->fresh()->hasPermission('manage-gallery'))->toBeTrue();

    $editor = User::factory()->editor()->create();

    expect($editor->hasPermission('manage-gallery'))->toBeTrue();
});

test('editors cannot invent permissions', function () {
    $user = User::factory()->editor()->create();

    $this->actingAs($user)->post(route('admin.permissions.store'), [
        'name' => 'manage-articles',
        'label' => 'Manage Articles',
    ])->assertForbidden();
});

test('system permissions cannot be deleted', function () {
    $user = User::factory()->superAdmin()->create();
    $permission = Permission::query()->where('name', 'manage-users')->firstOrFail();

    $this->actingAs($user)
        ->from(route('admin.permissions.index'))
        ->delete(route('admin.permissions.destroy', $permission))
        ->assertRedirect()
        ->assertSessionHasErrors('permission');

    $this->assertDatabaseHas('adminlte_permissions', ['name' => 'manage-users']);
});
