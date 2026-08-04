<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ForcePasswordChangeController extends Controller
{
    public function edit(): View
    {
        abort_unless(auth()->user()?->must_change_password, 403);

        return view('auth.force-password');
    }

    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user?->must_change_password, 403);

        $user->forceFill([
            'password' => $request->validated()['password'],
            'must_change_password' => false,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Password updated. Welcome aboard.');
    }
}
