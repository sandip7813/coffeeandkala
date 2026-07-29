<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorizeManage();

        $roles = Role::withCount('permissions')->orderBy('name')->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorizeManage();

        $permissions = Permission::orderBy('name')->get();

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

        $permissions = Permission::orderBy('name')->get();
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

        if ($role->name === 'super_admin') {
            $data['name'] = 'super_admin';
        }

        $role->update([
            'name' => $data['name'],
            'label' => $data['label'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

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

    private function guardProtectedRoleRename(Role $role, string $newName): void
    {
        if ($role->name === 'super_admin' && $newName !== 'super_admin') {
            throw ValidationException::withMessages([
                'name' => 'The super admin role name cannot be changed.',
            ]);
        }
    }
}
