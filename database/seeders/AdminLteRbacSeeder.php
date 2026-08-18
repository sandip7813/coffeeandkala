<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminLteRbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view-dashboard' => ['label' => 'View Dashboard', 'group' => 'Dashboard'],

            'manage-users' => ['label' => 'Manage Users', 'group' => 'Users'],
            'delete-users' => ['label' => 'Delete Users', 'group' => 'Users'],
            'change-user-status' => ['label' => 'Change User Status', 'group' => 'Users'],

            'edit-categories' => ['label' => 'Edit Categories', 'group' => 'Categories'],
            'change-category-status' => ['label' => 'Change Category Status', 'group' => 'Categories'],

            'view-quotes' => ['label' => 'Show Quotes', 'group' => 'Quotes'],
            'create-quotes' => ['label' => 'Add New Quote', 'group' => 'Quotes'],
            'assign-quote-dates' => ['label' => 'Assign Quote To Date', 'group' => 'Quotes'],
            'edit-quotes' => ['label' => 'Edit Quote', 'group' => 'Quotes'],
            'delete-quotes' => ['label' => 'Delete Quote', 'group' => 'Quotes'],

            'manage-roles' => ['label' => 'Manage Roles', 'group' => 'Roles & Permissions'],
            'manage-permissions' => ['label' => 'Manage Permissions', 'group' => 'Roles & Permissions'],

            'manage-settings' => ['label' => 'Manage Artisan Runner', 'group' => 'Artisan Runner'],
        ];

        foreach ($permissions as $name => $definition) {
            Permission::updateOrCreate(['name' => $name], [
                'label' => $definition['label'],
                'group' => $definition['group'],
            ]);
        }

        // Superseded by the granular 'view-quotes'/'create-quotes'/'edit-quotes'/
        // 'delete-quotes'/'assign-quote-dates' permissions above.
        Permission::where('name', 'manage-quotes')->delete();

        $roles = [
            'super_admin' => [
                'label' => 'Super Admin',
                'permissions' => array_keys($permissions),
            ],
            'admin' => [
                'label' => 'Admin',
                'permissions' => [
                    'view-dashboard',
                    'manage-users',
                    'manage-roles',
                ],
            ],
            'editor' => [
                'label' => 'Editor',
                'permissions' => [
                    'view-dashboard',
                ],
            ],
            'viewer' => [
                'label' => 'Viewer',
                'permissions' => [
                    'view-dashboard',
                ],
            ],
        ];

        foreach ($roles as $name => $definition) {
            $role = Role::firstOrCreate(['name' => $name], ['label' => $definition['label']]);

            $permissionIds = Permission::whereIn('name', $definition['permissions'])->pluck('id');
            $role->permissions()->sync($permissionIds);
        }
    }
}
