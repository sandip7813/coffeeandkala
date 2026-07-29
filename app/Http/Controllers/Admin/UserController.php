<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(): View
    {
        $this->authorizeManage();

        $users = User::with('roles')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $this->authorizeManage();

        $roles = $this->assignableRoles();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:adminlte_roles,id'],
        ]);

        $roleIds = $this->sanitizeRoleIds($data['roles'] ?? []);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
        ]);

        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users.index')
            ->with('status', __('adminlte.user_created'));
    }

    public function edit(User $user): View
    {
        $this->authorizeManage();

        $roles = $this->assignableRoles();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:adminlte_roles,id'],
        ]);

        $roleIds = $this->sanitizeRoleIds($data['roles'] ?? []);

        $this->guardLastSuperAdminRoleChange($user, $roleIds);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users.index')
            ->with('status', __('adminlte.user_updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeManage();

        if ($user->is(auth()->user())) {
            throw ValidationException::withMessages([
                'user' => 'You cannot delete your own account.',
            ]);
        }

        $this->guardLastSuperAdminDeletion($user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('status', __('adminlte.user_deleted'));
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->can('manage-users'), 403);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Role>
     */
    private function assignableRoles()
    {
        $query = Role::query()->orderBy('name');

        if (! auth()->user()?->isSuperAdmin()) {
            $query->where('name', '!=', 'super_admin');
        }

        return $query->get();
    }

    /**
     * @param  array<int, int|string>  $roleIds
     * @return array<int, int>
     */
    private function sanitizeRoleIds(array $roleIds): array
    {
        $roleIds = array_map('intval', $roleIds);

        if (auth()->user()?->isSuperAdmin()) {
            return $roleIds;
        }

        $superAdminRoleId = Role::query()->where('name', 'super_admin')->value('id');

        return array_values(array_filter(
            $roleIds,
            fn (int $id): bool => $id !== (int) $superAdminRoleId,
        ));
    }

    /**
     * @param  array<int, int>  $roleIds
     */
    private function guardLastSuperAdminRoleChange(User $user, array $roleIds): void
    {
        if (! $user->isSuperAdmin()) {
            return;
        }

        $superAdminRoleId = Role::query()->where('name', 'super_admin')->value('id');

        if ($superAdminRoleId === null || in_array((int) $superAdminRoleId, $roleIds, true)) {
            return;
        }

        if ($this->superAdminCount() <= 1) {
            throw ValidationException::withMessages([
                'roles' => 'Cannot remove the super admin role from the last super admin.',
            ]);
        }
    }

    private function guardLastSuperAdminDeletion(User $user): void
    {
        if (! $user->isSuperAdmin()) {
            return;
        }

        if ($this->superAdminCount() <= 1) {
            throw ValidationException::withMessages([
                'user' => 'Cannot delete the last super admin.',
            ]);
        }
    }

    private function superAdminCount(): int
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->where('name', 'super_admin'))
            ->count();
    }
}
