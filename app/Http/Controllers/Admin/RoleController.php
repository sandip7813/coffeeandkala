<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorizeManage();

        $roles = Role::with('permissions')->withCount('permissions')->orderBy('name')->paginate(15);
        $permissions = $this->groupedPermissions();

        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function create(): View
    {
        $this->authorizeManage();

        $permissions = $this->groupedPermissions();

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:adminlte_roles,name', 'not_in:super_admin'],
            'label' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:adminlte_permissions,id'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'label' => $data['label'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return redirect()->route('admin.roles.index')
            ->with('status', __('adminlte.role_created'));
    }

    public function edit(Role $role): View
    {
        $this->authorizeManage();

        $permissions = $this->groupedPermissions();
        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->authorizeManage();

        $this->guardProtectedRoleRename($role, $request->string('name')->toString());

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:adminlte_roles,name,'.$role->id],
            'label' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:adminlte_permissions,id'],
        ]);

        $isSuperAdmin = $role->name === 'super_admin';

        if ($isSuperAdmin) {
            $data['name'] = 'super_admin';
        }

        $role->update([
            'name' => $data['name'],
            'label' => $data['label'] ?? null,
        ]);

        // Super Admin isn't editable from the form (the checkboxes aren't even
        // rendered for it), so keep it synced to every permission that exists
        // rather than trusting — or wiping it out with — whatever was submitted.
        $role->permissions()->sync($isSuperAdmin ? Permission::pluck('id') : ($data['permissions'] ?? []));

        return redirect()->route('admin.roles.index')
            ->with('status', __('adminlte.role_updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->authorizeManage();

        if ($role->name === 'super_admin') {
            throw ValidationException::withMessages([
                'role' => 'The super admin role cannot be deleted.',
            ]);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('status', __('adminlte.role_deleted'));
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->can('manage-roles'), 403);
    }

    /**
     * All permissions, grouped by their `group` label for a grouped checkbox
     * list — ungrouped permissions fall under a catch-all "General" heading.
     * Groups follow Permission::GROUP_ORDER rather than alphabetical order.
     *
     * @return Collection<string, Collection<int, Permission>>
     */
    private function groupedPermissions()
    {
        return Permission::allOrderedByGroup()
            ->groupBy(fn (Permission $permission) => $permission->group ?? 'General');
    }

    private function guardProtectedRoleRename(Role $role, string $newName): void
    {
        if ($role->name === 'super_admin' && $newName !== 'super_admin') {
            throw ValidationException::withMessages([
                'name' => 'The super admin role name cannot be changed.',
            ]);
        }
    }
}
