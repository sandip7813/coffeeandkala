Hello {{ $user->full_name }},

@if ($activated)
Your Coffee & Kala admin account has been reactivated. You can sign in again using your existing credentials.
@else
Your Coffee & Kala admin account has been deactivated. You will not be able to sign in to the admin panel until an administrator reactivates your account.
@endif

Email: {{ $user->email }}

@if ($activated)
Sign in: {{ route('login') }}
@endif

If you believe this is a mistake, please contact an administrator.

— {{ config('app.name') }}
  Editorial journal of slow living, art, and quiet stories.
