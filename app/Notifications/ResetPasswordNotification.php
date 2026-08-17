<?php

namespace App\Notifications;

use App\Mail\ResetPasswordMail;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Contracts\Mail\Mailable;

class ResetPasswordNotification extends BaseResetPassword
{
    /**
     * @param  mixed  $notifiable
     */
    public function toMail($notifiable): Mailable
    {
        return new ResetPasswordMail(
            $notifiable,
            $this->resetUrl($notifiable),
            (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
        );
    }
}
