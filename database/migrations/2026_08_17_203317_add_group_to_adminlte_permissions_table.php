<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adminlte_permissions', function (Blueprint $table) {
            $table->string('group')->nullable()->after('label');
        });

        // Backfill groups for permissions that may already exist in the
        // database, so the grouped listing isn't left blank until reseeded.
        $groups = [
            'view-dashboard' => 'Dashboard',
            'manage-users' => 'Users',
            'delete-users' => 'Users',
            'change-user-status' => 'Users',
            'edit-categories' => 'Categories',
            'change-category-status' => 'Categories',
            'view-quotes' => 'Quotes',
            'create-quotes' => 'Quotes',
            'assign-quote-dates' => 'Quotes',
            'edit-quotes' => 'Quotes',
            'delete-quotes' => 'Quotes',
            'manage-roles' => 'Roles & Permissions',
            'manage-permissions' => 'Roles & Permissions',
            'manage-settings' => 'Artisan Runner',
        ];

        foreach ($groups as $name => $group) {
            DB::table('adminlte_permissions')->where('name', $name)->update(['group' => $group]);
        }
    }

    public function down(): void
    {
        Schema::table('adminlte_permissions', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
