<?php

namespace App\Http\Controllers\Admin;

use App\Actions\UpdateProfilePhoto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Http\Requests\Admin\UpdateProfilePhotoRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());
        $user->save();

        return redirect()
            ->route('admin.profile.edit')
            ->with('status', 'Profile updated.');
    }

    public function updatePhoto(UpdateProfilePhotoRequest $request, UpdateProfilePhoto $updateProfilePhoto): RedirectResponse
    {
        $updateProfilePhoto->handle($request->user(), $request->file('profile_photo'));

        return redirect()
            ->route('admin.profile.edit')
            ->with('status', 'Profile picture updated.');
    }

    public function editPassword(): View
    {
        return view('admin.profile.password');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated()['password'],
        ]);

        return redirect()
            ->route('admin.profile.password.edit')
            ->with('status', 'Password updated.');
    }
}
