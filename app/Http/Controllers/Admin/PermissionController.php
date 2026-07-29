<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PermissionController extends Controller
{
    public function index(): View
    {
        $this->authorizeManage();

        $permissions = Permission::query()->orderBy('name')->paginate(20);

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        $this->authorizeManage();

        return view('admin.permissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9][a-z0-9._-]*$/i', 'unique:adminlte_permissions,name'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        Permission::create([
            'name' => $data['name'],
            'label' => $data['label'] ?? null,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('status', __('Permission created.'));
    }

    public function edit(Permission $permission): View
    {
        $this->authorizeManage();

        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9][a-z0-9._-]*$/i',
                Rule::unique('adminlte_permissions', 'name')->ignore($permission->id),
            ],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->isProtectedPermission($permission->name) && $data['name'] !== $permission->name) {
            throw ValidationException::withMessages([
                'name' => 'This system permission name cannot be changed.',
            ]);
        }

        $permission->update([
            'name' => $this->isProtectedPermission($permission->name) ? $permission->name : $data['name'],
            'label' => $data['label'] ?? null,
        ]);

        return redirect()->route('admin.permissions.index')
            ->with('status', __('Permission updated.'));
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $this->authorizeManage();

        if ($this->isProtectedPermission($permission->name)) {
            throw ValidationException::withMessages([
                'permission' => 'This system permission cannot be deleted.',
            ]);
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('status', __('Permission deleted.'));
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->can('manage-permissions'), 403);
    }

    private function isProtectedPermission(string $name): bool
    {
        return in_array($name, [
            'view-dashboard',
            'manage-users',
            'manage-roles',
            'manage-permissions',
        ], true);
    }
}
