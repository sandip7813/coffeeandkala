<?php

namespace App\Http\Controllers\Admin;

use App\Actions\IssueOneTimePassword;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
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

    public function store(Request $request, IssueOneTimePassword $issueOneTimePassword): RedirectResponse
    {
        $this->authorizeManage();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'digits_between:1,10'],
            'role' => ['nullable', 'integer', $this->assignableRoleRule()],
        ]);

        $roleIds = $this->sanitizeRoleIds(
            isset($data['role']) ? [(int) $data['role']] : []
        );

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => filled($data['phone'] ?? null) ? $data['phone'] : null,
            'password' => Str::random(32),
            'is_active' => true,
            'must_change_password' => true,
            'email_verified_at' => null,
        ]);

        $user->roles()->sync($roleIds);

        $issueOneTimePassword->handle($user, 'account');

        return redirect()->route('admin.users.index')
            ->with('status', __('adminlte.user_created').' A one-time password has been emailed to the user.');
    }

    public function edit(User $user): View
    {
        $this->authorizeManage();

        $roles = $this->assignableRoles();
        $user->load('roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user, IssueOneTimePassword $issueOneTimePassword): RedirectResponse
    {
        $this->authorizeManage();

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'digits_between:1,10'],
            'role' => ['nullable', 'integer', $this->assignableRoleRule()],
        ];

        if (auth()->user()?->isSuperAdmin()) {
            $rules['is_active'] = ['sometimes', 'boolean'];
        }

        $data = $request->validate($rules);

        $emailChanged = $user->email !== $data['email'];

        $attributes = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => filled($data['phone'] ?? null) ? $data['phone'] : null,
        ];

        if (auth()->user()?->isSuperAdmin()
            && array_key_exists('is_active', $data)
            && ! $user->is(auth()->user())) {
            $attributes['is_active'] = $request->boolean('is_active');
        }

        $user->update($attributes);

        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
        }

        if ($user->isSuperAdmin()) {
            if (array_key_exists('role', $data) && $data['role'] !== null) {
                throw ValidationException::withMessages([
                    'role' => 'Super admin accounts cannot be changed to another role.',
                ]);
            }
        } else {
            $roleIds = $this->sanitizeRoleIds(
                isset($data['role']) ? [(int) $data['role']] : []
            );

            $user->roles()->sync($roleIds);
        }

        $status = __('adminlte.user_updated');

        if ($emailChanged) {
            $issueOneTimePassword->handle($user->fresh(), 'email_changed');
            $status .= ' A one-time password has been emailed to the new address.';
        }

        return redirect()->route('admin.users.index')
            ->with('status', $status);
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorizeManage();
        abort_unless(auth()->user()?->isSuperAdmin(), 403);

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
     * Roles that may be assigned when creating or editing users.
     * Super Admin is seeded only and cannot be assigned from the UI.
     *
     * @return Collection<int, Role>
     */
    private function assignableRoles()
    {
        return Role::query()
            ->where('name', '!=', 'super_admin')
            ->orderBy('name')
            ->get();
    }

    private function assignableRoleRule(): Exists
    {
        return Rule::exists('adminlte_roles', 'id')->where(
            fn ($query) => $query->where('name', '!=', 'super_admin')
        );
    }

    /**
     * @param  array<int, int|string>  $roleIds
     * @return array<int, int>
     */
    private function sanitizeRoleIds(array $roleIds): array
    {
        $roleIds = array_map('intval', $roleIds);
        $superAdminRoleId = (int) Role::query()->where('name', 'super_admin')->value('id');

        return array_values(array_filter(
            $roleIds,
            fn (int $id): bool => $id !== $superAdminRoleId,
        ));
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
