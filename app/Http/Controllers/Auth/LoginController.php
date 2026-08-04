<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Max failed attempts (per email + IP) before a timed lockout kicks in.
     */
    protected int $maxAttempts = 5;

    public function showLoginForm(): View
    {
        return view('adminlte::auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $this->ensureIsNotRateLimited($request);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user === null || ! $user->is_active) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => 'Your account is inactive. Please contact an administrator.',
                ]);
            }

            if (! $user->isSuperAdmin() && ! $user->hasPermission('view-dashboard')) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'email' => 'You do not have access to the admin panel.',
                ]);
            }

            RateLimiter::clear($this->throttleKey($request));
            $request->session()->regenerate();

            if ($user->must_change_password) {
                return redirect()->route('password.force.edit');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($this->throttleKey($request));

        return back()->withErrors([
            'email' => __('auth.failed'),
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Throw a throttled validation error once too many failed attempts pile up.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), $this->maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip());
    }
}
