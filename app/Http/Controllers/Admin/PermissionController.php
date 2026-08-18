<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PermissionController extends Controller
{
    public function index(): View
    {
        $this->authorizeManage();

        // Grouped by the canonical order (Permission::GROUP_ORDER) rather than
        // alphabetically, so this can't be expressed as a plain DB ->orderBy().
        $perPage = 20;
        $page = request()->integer('page', 1);
        $ordered = Permission::allOrderedByGroup();

        $permissions = new LengthAwarePaginator(
            $ordered->forPage($page, $perPage)->values(),
            $ordered->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()],
        );

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
            'group' => ['nullable', 'string', 'max:255'],
        ]);

        Permission::create([
            'name' => $data['name'],
            'label' => $data['label'] ?? null,
            'group' => $data['group'] ?? null,
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
            'group' => ['nullable', 'string', 'max:255'],
        ]);

        if ($this->isProtectedPermission($permission->name) && $data['name'] !== $permission->name) {
            throw ValidationException::withMessages([
                'name' => 'This system permission name cannot be changed.',
            ]);
        }

        $permission->update([
            'name' => $this->isProtectedPermission($permission->name) ? $permission->name : $data['name'],
            'label' => $data['label'] ?? null,
            'group' => $data['group'] ?? null,
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
            'delete-users',
            'change-user-status',
            'edit-categories',
            'change-category-status',
            'view-quotes',
            'create-quotes',
            'assign-quote-dates',
            'edit-quotes',
            'delete-quotes',
            'manage-roles',
            'manage-permissions',
        ], true);
    }
}
