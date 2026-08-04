<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ config('app.name') }}</title>
    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <!--<![endif]-->
    <style type="text/css">
        body { margin: 0; padding: 0; background: #5B3A29; -webkit-text-size-adjust: 100%; }
        table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { border: 0; display: block; line-height: 100%; outline: none; text-decoration: none; }
        a { color: #E7DDD2; }
    </style>
</head>
<body style="margin:0;padding:0;background:#5B3A29;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#5B3A29;">
    <tr>
        <td align="center" style="padding:48px 16px;">

            {{-- Outer elegant gold frame --}}
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:540px;background:#C4A574;border-collapse:separate;">
                <tr>
                    <td style="padding:1px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#5B3A29;border-collapse:separate;">
                            <tr>
                                <td style="padding:14px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#C4A574;border-collapse:separate;">
                                        <tr>
                                            <td style="padding:1px;">
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#5B3A29;">

                                                    {{-- Monogram --}}
                                                    <tr>
                                                        <td align="center" style="padding:40px 36px 12px;background:#5B3A29;">
                                                            <img
                                                                src="{{ $message->embed(\App\Support\BrandLogo::emailAbsolutePath()) }}"
                                                                width="96"
                                                                height="97"
                                                                alt="Coffee &amp; Kala"
                                                                style="width:96px;height:auto;"
                                                            >
                                                        </td>
                                                    </tr>

                                                    {{-- Brand --}}
                                                    <tr>
                                                        <td align="center" style="padding:8px 36px 0;background:#5B3A29;">
                                                            <p style="margin:0;font-family:'Cormorant Garamond',Georgia,'Times New Roman',serif;font-size:26px;font-weight:400;letter-spacing:0.06em;color:#E7DDD2;">
                                                                Coffee &amp; Kala
                                                            </p>
                                                            <p style="margin:10px 0 0;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:10px;font-weight:600;letter-spacing:0.28em;text-transform:uppercase;color:#C4A574;">
                                                                Admin access
                                                            </p>
                                                        </td>
                                                    </tr>

                                                    {{-- Rule --}}
                                                    <tr>
                                                        <td align="center" style="padding:22px 36px 8px;background:#5B3A29;">
                                                            <div style="width:56px;height:1px;background:#C4A574;margin:0 auto;"></div>
                                                        </td>
                                                    </tr>

                                                    {{-- Headline --}}
                                                    <tr>
                                                        <td align="center" style="padding:16px 40px 8px;background:#5B3A29;">
                                                            <h1 style="margin:0;font-family:'Cormorant Garamond',Georgia,'Times New Roman',serif;font-size:36px;font-weight:400;line-height:1.2;color:#FFFEFA;">
                                                                @if ($reason === 'email_changed')
                                                                    Your email was updated
                                                                @else
                                                                    Welcome aboard
                                                                @endif
                                                            </h1>
                                                        </td>
                                                    </tr>

                                                    {{-- Body --}}
                                                    <tr>
                                                        <td style="padding:18px 44px 28px;background:#5B3A29;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:15px;line-height:1.75;color:#E7DDD2;text-align:center;">
                                                            <p style="margin:0 0 16px;color:#FFFEFA;font-size:16px;">
                                                                Dear {{ $user->full_name }},
                                                            </p>
                                                            @if ($reason === 'email_changed')
                                                                <p style="margin:0;">
                                                                    Your Coffee &amp; Kala admin email has changed. Use the one-time password below to sign in, then set a new password for your account.
                                                                </p>
                                                            @else
                                                                <p style="margin:0;">
                                                                    An admin account has been created for you on Coffee &amp; Kala. Sign in once with the credentials below — you’ll choose your own password right after.
                                                                </p>
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    {{-- Credential panel --}}
                                                    <tr>
                                                        <td style="padding:0 44px 30px;background:#5B3A29;">
                                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#C4A574;">
                                                                <tr>
                                                                    <td style="padding:1px;">
                                                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#4A2F21;">
                                                                            <tr>
                                                                                <td style="padding:28px 24px;text-align:center;">
                                                                                    <p style="margin:0 0 10px;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:10px;font-weight:600;letter-spacing:0.22em;text-transform:uppercase;color:#C4A574;">
                                                                                        One-time password
                                                                                    </p>
                                                                                    <p style="margin:0 0 22px;font-family:Georgia,'Times New Roman',serif;font-size:28px;font-weight:600;letter-spacing:0.14em;color:#FFFEFA;line-height:1.2;">
                                                                                        {{ $oneTimePassword }}
                                                                                    </p>
                                                                                    <div style="width:36px;height:1px;background:#C4A574;margin:0 auto 18px;"></div>
                                                                                    <p style="margin:0 0 6px;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:10px;font-weight:600;letter-spacing:0.22em;text-transform:uppercase;color:#C4A574;">
                                                                                        Sign-in email
                                                                                    </p>
                                                                                    <p style="margin:0;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:14px;font-weight:500;color:#E7DDD2;word-break:break-all;">
                                                                                        {{ $user->email }}
                                                                                    </p>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>

                                                    {{-- CTA --}}
                                                    <tr>
                                                        <td align="center" style="padding:0 44px 28px;background:#5B3A29;">
                                                            <table role="presentation" cellpadding="0" cellspacing="0" style="background:#C4A574;">
                                                                <tr>
                                                                    <td style="padding:1px;">
                                                                        <table role="presentation" cellpadding="0" cellspacing="0" style="background:#5B3A29;">
                                                                            <tr>
                                                                                <td align="center">
                                                                                    <a href="{{ route('login') }}"
                                                                                       style="display:inline-block;padding:15px 38px;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:12px;font-weight:600;letter-spacing:0.18em;text-transform:uppercase;text-decoration:none;color:#E7DDD2;">
                                                                                        Sign in to admin
                                                                                    </a>
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>

                                                    {{-- Note --}}
                                                    <tr>
                                                        <td style="padding:0 44px 40px;background:#5B3A29;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:13px;line-height:1.65;color:#C9B8A6;text-align:center;">
                                                            This password is temporary and meant for a single first sign-in. You’ll create a lasting password before entering the dashboard.
                                                        </td>
                                                    </tr>

                                                    {{-- Footer rule + mark --}}
                                                    <tr>
                                                        <td align="center" style="padding:0 40px 36px;background:#5B3A29;">
                                                            <div style="width:56px;height:1px;background:#C4A574;margin:0 auto 18px;"></div>
                                                            <p style="margin:0 0 6px;font-family:'Cormorant Garamond',Georgia,'Times New Roman',serif;font-size:18px;font-style:italic;color:#E7DDD2;">
                                                                Coffee &amp; Kala
                                                            </p>
                                                            <p style="margin:0 0 12px;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:12px;color:#C9B8A6;line-height:1.5;">
                                                                An editorial journal of slow living, art &amp; quiet stories.
                                                            </p>
                                                            <p style="margin:0;font-family:'Plus Jakarta Sans',Helvetica,Arial,sans-serif;font-size:12px;">
                                                                <a href="{{ config('app.url') }}" style="color:#C4A574;text-decoration:none;">{{ parse_url(config('app.url'), PHP_URL_HOST) ?: config('app.url') }}</a>
                                                            </p>
                                                        </td>
                                                    </tr>

                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>
