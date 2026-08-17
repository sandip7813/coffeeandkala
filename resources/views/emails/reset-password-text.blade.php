Hello {{ $user->full_name }},

You are receiving this email because we received a password reset request for your account.

Reset your password: {{ $resetUrl }}

This password reset link will expire in {{ $expiresInMinutes }} minutes.

If you did not request a password reset, no further action is required.

— {{ config('app.name') }}
  Editorial journal of slow living, art, and quiet stories.
