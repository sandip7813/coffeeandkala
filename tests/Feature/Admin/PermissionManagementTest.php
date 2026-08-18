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

test('the permissions list is grouped by section', function () {
    $user = User::factory()->superAdmin()->create();

    // The Users group now falls entirely on page 2 of the 20-per-page listing
    // — with Gallery/Studio's permissions added, the first 20 rows end
    // exactly at Categories — so it's checked separately from page 1.
    $this->actingAs($user)
        ->get(route('admin.permissions.index'))
        ->assertOk()
        ->assertSeeTextInOrder(['Categories', 'Change Category Status', 'Edit Categories'])
        ->assertSeeTextInOrder(['Quotes', 'Add New Quote', 'Assign Quote To Date', 'Delete Quote', 'Edit Quote', 'Show Quotes']);

    $this->actingAs($user)
        ->get(route('admin.permissions.index', ['page' => 2]))
        ->assertOk()
        ->assertSeeTextInOrder(['Users', 'Change User Status', 'Delete Users', 'Manage Users']);
});

test('the role permission checkboxes are grouped by section', function () {
    $user = User::factory()->superAdmin()->create();

    $this->actingAs($user)
        ->get(route('admin.roles.create'))
        ->assertOk()
        ->assertSeeTextInOrder(['Categories', 'Change Category Status', 'Edit Categories'])
        ->assertSeeTextInOrder(['Quotes', 'Add New Quote', 'Assign Quote To Date', 'Delete Quote', 'Edit Quote', 'Show Quotes']);
});

test('permission groups follow the configured display order, not alphabetical', function () {
    $user = User::factory()->superAdmin()->create();
    $order = ['Dashboard', 'Quotes', 'Gallery', 'Studio', 'Categories', 'Users', 'Roles & Permissions', 'Artisan Runner'];

    // admin.permissions.index paginates at 20 rows; with the full permission
    // set now at 26, the last three groups spill onto page 2 — check each
    // page's slice of the order separately instead of the whole list at once.
    $this->actingAs($user)
        ->get(route('admin.permissions.index'))
        ->assertOk()
        ->assertSeeTextInOrder(['Dashboard', 'Quotes', 'Gallery', 'Studio', 'Categories']);

    $this->actingAs($user)
        ->get(route('admin.permissions.index', ['page' => 2]))
        ->assertOk()
        ->assertSeeTextInOrder(['Users', 'Roles & Permissions', 'Artisan Runner']);

    $this->actingAs($user)
        ->get(route('admin.roles.create'))
        ->assertOk()
        ->assertSeeTextInOrder($order);
});

test('a newly created permission is automatically granted to super admin', function () {
    $superAdminRole = Role::query()->where('name', 'super_admin')->firstOrFail();

    $permission = Permission::create([
        'name' => 'manage-newsletter',
        'label' => 'Manage Newsletter',
    ]);

    expect($superAdminRole->fresh()->hasPermission('manage-newsletter'))->toBeTrue();

    $superAdmin = User::factory()->superAdmin()->create();
    expect($superAdmin->hasPermission('manage-newsletter'))->toBeTrue();
});

test('the super admin role edit form has no permission checkboxes to change', function () {
    $user = User::factory()->superAdmin()->create();
    $role = Role::query()->where('name', 'super_admin')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.roles.edit', $role))
        ->assertOk()
        ->assertSee('Super Admin always has every permission.')
        ->assertDontSee('name="permissions[]"', false);
});

test('submitting the super admin role form cannot strip its permissions', function () {
    $user = User::factory()->superAdmin()->create();
    $role = Role::query()->where('name', 'super_admin')->firstOrFail();
    $totalPermissions = Permission::count();

    // Simulates the real form: no `permissions[]` fields are rendered for
    // super admin, so nothing is submitted for that field either.
    $this->actingAs($user)
        ->put(route('admin.roles.update', $role), [
            'name' => 'super_admin',
            'label' => 'Super Admin',
        ])
        ->assertRedirect(route('admin.roles.index'));

    expect($role->fresh()->permissions()->count())->toBe($totalPermissions);
});

test('the roles list offers a permissions modal for each role', function () {
    $user = User::factory()->superAdmin()->create();
    $role = Role::query()->where('name', 'editor')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertOk()
        ->assertSee('id="role-permissions-'.$role->id.'"', false)
        ->assertSee('data-bs-target="#role-permissions-'.$role->id.'"', false);
});

test('the roles list has no permissions modal for super admin', function () {
    $user = User::factory()->superAdmin()->create();
    $role = Role::query()->where('name', 'super_admin')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertOk()
        ->assertDontSee('id="role-permissions-'.$role->id.'"', false)
        ->assertDontSee('data-bs-target="#role-permissions-'.$role->id.'"', false);
});

test('a role\'s permissions can be updated from its modal on the roles list', function () {
    $user = User::factory()->superAdmin()->create();
    $role = Role::query()->where('name', 'editor')->firstOrFail();
    $permission = Permission::query()->where('name', 'view-quotes')->firstOrFail();

    $this->actingAs($user)->put(route('admin.roles.update', $role), [
        'name' => $role->name,
        'label' => $role->label,
        'permissions' => array_merge($role->permissions->pluck('id')->all(), [$permission->id]),
        '_reopen_modal' => 'role-permissions-'.$role->id,
    ])->assertRedirect(route('admin.roles.index'));

    expect($role->fresh()->hasPermission('view-quotes'))->toBeTrue();
});
