<?php

use App\Mail\ResetPasswordMail;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(fn () => seedRbac());

test('requesting a password reset link sends the branded reset password notification', function () {
    Notification::fake();

    $user = User::factory()->editor()->create(['email' => 'reset-me@example.com']);

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertSessionHasNoErrors();

    Notification::assertSentTo($user, function (ResetPasswordNotification $notification) use ($user): bool {
        $mail = $notification->toMail($user);

        return $mail instanceof ResetPasswordMail
            && $mail->hasTo($user->email)
            && $mail->user->is($user)
            && str_contains($mail->resetUrl, '/reset-password/')
            && $mail->expiresInMinutes > 0;
    });
});
