<?php

use App\Mail\OneTimePasswordMail;
use App\Models\User;

test('one time password mail renders branded html content', function () {
    $user = new User([
        'first_name' => 'Asha',
        'last_name' => 'Roy',
        'email' => 'asha@example.com',
    ]);

    $mailable = new OneTimePasswordMail($user, 'TempPass1234', 'account');

    $mailable->assertSeeInHtml('Welcome aboard');
    $mailable->assertSeeInHtml('Coffee & Kala');
    $mailable->assertSeeInHtml('asha@example.com');
    $mailable->assertSeeInHtml('TempPass1234');
    $mailable->assertSeeInHtml('Sign in to admin');
    $mailable->assertSeeInHtml('One-time password');
    $mailable->assertSeeInHtml('#5B3A29');
    $mailable->assertSeeInText('One-time password: TempPass1234');

    expect(is_file(public_path('images/logo/monogram-email.png')))->toBeTrue();
});
