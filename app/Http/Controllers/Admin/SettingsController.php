<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBrandLogoRequest;
use App\Http\Requests\Admin\UpdateSocialLinksRequest;
use App\Support\BrandLogo;
use App\Support\SocialLinks;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'logos' => BrandLogo::options(),
            'selectedLogo' => BrandLogo::currentKey(),
            'socialNetworks' => SocialLinks::networks(),
            'socialLinks' => SocialLinks::all(),
        ]);
    }

    public function updateLogo(UpdateBrandLogoRequest $request): RedirectResponse
    {
        BrandLogo::set($request->validated('logo'));

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Brand logo updated.');
    }

    public function updateSocial(UpdateSocialLinksRequest $request): RedirectResponse
    {
        SocialLinks::set($request->validated('links'));

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Social media links updated.');
    }
}
