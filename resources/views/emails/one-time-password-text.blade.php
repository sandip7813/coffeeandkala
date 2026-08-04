Hello {{ $user->full_name }},

@if ($reason === 'email_changed')
Your Coffee & Kala admin email was updated. Sign in with the one-time password below, then choose a new password.
@else
An admin account has been created for you on Coffee & Kala. Sign in with the one-time password below, then choose a new password.
@endif

Email: {{ $user->email }}
One-time password: {{ $oneTimePassword }}

Sign in: {{ route('login') }}

This password is temporary. You will be asked to change it after signing in.

— {{ config('app.name') }}
  Editorial journal of slow living, art, and quiet stories.
