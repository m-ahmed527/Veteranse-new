<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 30px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; padding: 40px; border-radius: 6px;">
                    {{-- <tr>
                        <td align="center" style="padding-bottom: 20px;">
                            <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel Logo" width="40">
                        </td>
                    </tr> --}}
                    <tr>
                        <td style="font-size: 18px; font-weight: bold; padding-bottom: 10px;">
                            Hello!
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; padding-bottom: 20px;">
                            You are receiving this email because we received a password reset request for your account.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-bottom: 20px;">
                            <a href="{{ url('/password/reset', ['token' => $token]) }}"
                                style="background-color: #1a202c; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; display: inline-block;">
                                Reset Password
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; padding-bottom: 10px;">
                            This password reset link will expire in 60 minutes.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; padding-bottom: 20px;">
                            If you did not request a password reset, no further action is required.
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px; padding-bottom: 30px;">
                            Regards,<br>
                            Laravel
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 12px; color: #555; border-top: 1px solid #ddd; padding-top: 20px;">
                            If you're having trouble clicking the "Reset Password" button, copy and paste the URL below
                            into your web browser:<br>
                            <a href="{{ url('/password/reset', ['token' => $token]) }}"
                                style="color: #3869D4;">{{ url('/password/reset', ['token' => $token]) }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px; color: #999; padding-top: 30px;">
                            © {{ date('Y') }} Laravel. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
