<?php

namespace App\Http\Controllers\Admin;

use App\Actions\IssueOneTimePassword;
use App\Actions\UpdateProfilePhoto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfilePhotoRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeManage();

        $filters = $request->only(['name', 'role', 'status']);

        $users = User::query()
            ->with('roles')
            ->when(filled($filters['name'] ?? null), function ($query) use ($filters) {
                $query->where(function ($query) use ($filters) {
                    $query->where('first_name', 'like', '%'.$filters['name'].'%')
                        ->orWhere('last_name', 'like', '%'.$filters['name'].'%')
                        ->orWhere('email', 'like', '%'.$filters['name'].'%');
                });
            })
            ->when(filled($filters['role'] ?? null), fn ($query) => $query->whereHas(
                'roles', fn ($query) => $query->where('name', $filters['role'])
            ))
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('is_active', $filters['status'] === 'active'))
            ->latest()
            ->paginate(config('pagination.admin.users_per_page'))
            ->withQueryString();

        $roles = $this->assignableRoles();

        $hasActiveFilters = collect($filters)->filter(fn ($value) => filled($value))->isNotEmpty();

        // The name/email filter value is a user's email (selected from the
        // Select2 autocomplete), but the field should keep showing the
        // "Full Name (email)" label the user picked rather than just the
        // raw email once the page reloads.
        $nameFilterLabel = null;

        if (filled($filters['name'] ?? null)) {
            $matchedUser = User::query()->where('email', $filters['name'])->first();
            $nameFilterLabel = $matchedUser
                ? "{$matchedUser->full_name} ({$matchedUser->email})"
                : $filters['name'];
        }

        return view('admin.users.index', compact('users', 'roles', 'filters', 'hasActiveFilters', 'nameFilterLabel'));
    }

    /**
     * Select2 AJAX source for the name/email search field on the users index.
     * Returns matches once at least 3 characters have been typed.
     */
    public function search(Request $request): JsonResponse
    {
        $this->authorizeManage();

        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 3) {
            return response()->json(['results' => []]);
        }

        $users = User::query()
            ->where('first_name', 'like', '%'.$term.'%')
            ->orWhere('last_name', 'like', '%'.$term.'%')
            ->orWhere('email', 'like', '%'.$term.'%')
            ->orderBy('first_name')
            ->limit(20)
            ->get(['first_name', 'last_name', 'email']);

        return response()->json([
            'results' => $users->map(fn (User $user): array => [
                'id' => $user->email,
                'text' => "{$user->full_name} ({$user->email})",
            ])->values(),
        ]);
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

    public function updatePhoto(UpdateProfilePhotoRequest $request, User $user, UpdateProfilePhoto $updateProfilePhoto): RedirectResponse
    {
        $this->authorizeManage();

        $updateProfilePhoto->handle($user, $request->file('profile_photo'));

        return redirect()->back()
            ->with('status', 'Profile picture updated.');
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
