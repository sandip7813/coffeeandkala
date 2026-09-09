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

            'view-gallery' => ['label' => 'View Gallery', 'group' => 'Gallery'],
            'upload-gallery' => ['label' => 'Upload Gallery Image', 'group' => 'Gallery'],
            'edit-gallery' => ['label' => 'Edit Gallery Image', 'group' => 'Gallery'],
            'delete-gallery' => ['label' => 'Delete Gallery Image', 'group' => 'Gallery'],
            'change-gallery-status' => ['label' => 'Change Gallery Image Status', 'group' => 'Gallery'],
            'approve-gallery' => ['label' => 'Approve Gallery Image', 'group' => 'Gallery'],

            'view-studio' => ['label' => 'View Studio', 'group' => 'Studio'],
            'upload-studio' => ['label' => 'Upload Studio Image', 'group' => 'Studio'],
            'edit-studio' => ['label' => 'Edit Studio Image', 'group' => 'Studio'],
            'delete-studio' => ['label' => 'Delete Studio Image', 'group' => 'Studio'],
            'change-studio-status' => ['label' => 'Change Studio Image Status', 'group' => 'Studio'],
            'approve-studio' => ['label' => 'Approve Studio Image', 'group' => 'Studio'],

            'view-features' => ['label' => 'View Features', 'group' => 'Features'],
            'create-features' => ['label' => 'Create Feature Article', 'group' => 'Features'],
            'edit-features' => ['label' => 'Edit Feature Article', 'group' => 'Features'],
            'delete-features' => ['label' => 'Delete Feature Article', 'group' => 'Features'],
            'change-features-status' => ['label' => 'Change Feature Article Status', 'group' => 'Features'],
            'approve-features' => ['label' => 'Approve Feature Article', 'group' => 'Features'],

            'view-journals' => ['label' => 'View Journals', 'group' => 'Journals'],
            'create-journals' => ['label' => 'Create Journal Article', 'group' => 'Journals'],
            'edit-journals' => ['label' => 'Edit Journal Article', 'group' => 'Journals'],
            'delete-journals' => ['label' => 'Delete Journal Article', 'group' => 'Journals'],
            'change-journals-status' => ['label' => 'Change Journal Article Status', 'group' => 'Journals'],
            'approve-journals' => ['label' => 'Approve Journal Article', 'group' => 'Journals'],

            'view-poetry' => ['label' => 'View Poetry', 'group' => 'Poetry'],
            'upload-poetry' => ['label' => 'Add Poem', 'group' => 'Poetry'],
            'edit-poetry' => ['label' => 'Edit Poem', 'group' => 'Poetry'],
            'delete-poetry' => ['label' => 'Delete Poem', 'group' => 'Poetry'],
            'change-poetry-status' => ['label' => 'Change Poem Status', 'group' => 'Poetry'],
            'approve-poetry' => ['label' => 'Approve Poem', 'group' => 'Poetry'],

            'manage-home-sections' => ['label' => 'Manage Home Page Sections', 'group' => 'Home Page'],

            'manage-our-story' => ['label' => 'Manage Our Story', 'group' => 'Our Story'],

            'manage-meta' => ['label' => 'Manage Meta Data', 'group' => 'Meta Data'],

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
                    'view-gallery',
                    'view-studio',
                    'view-journals',
                    'view-features',
                    'view-poetry',
                ],
            ],
            'viewer' => [
                'label' => 'Viewer',
                'permissions' => [
                    'view-dashboard',
                    'view-gallery',
                    'view-studio',
                    'view-journals',
                    'view-features',
                    'view-poetry',
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
