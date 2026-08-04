<?php

namespace App\Actions;

use App\Mail\OneTimePasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class IssueOneTimePassword
{
    /**
     * Set a one-time password on the user, require a password change, and email the credentials.
     */
    public function handle(User $user, string $reason = 'account'): string
    {
        $oneTimePassword = Str::password(12, symbols: false);

        $user->forceFill([
            'password' => $oneTimePassword,
            'must_change_password' => true,
        ])->save();

        Mail::to($user->email)->send(new OneTimePasswordMail($user, $oneTimePassword, $reason));

        return $oneTimePassword;
    }
}
