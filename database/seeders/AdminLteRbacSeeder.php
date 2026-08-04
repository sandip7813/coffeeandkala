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
            'view-dashboard' => 'View Dashboard',
            'manage-users' => 'Manage Users',
            'manage-roles' => 'Manage Roles',
            'manage-permissions' => 'Manage Permissions',
            'manage-settings' => 'Manage Settings',
        ];

        foreach ($permissions as $name => $label) {
            Permission::firstOrCreate(['name' => $name], ['label' => $label]);
        }

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
                    'manage-settings',
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
