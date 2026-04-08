<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0F0F0F; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #0F0F0F; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-w-md w-full mx-auto max-width: 600px; background-color: #141414; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 40px 0;">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="background-color: #1392EC; padding: 12px; border-radius: 12px;">
                                        <!-- Lock/Shield Icon Fallback -->
                                        <img src="https://fonts.gstatic.com/s/i/short-term/release/materialsymbolsoutlined/lock_reset/default/48px.svg" width="32" height="32" style="display: block; filter: invert(1);" alt="Reset Password">
                                    </td>
                                </tr>
                            </table>
                            <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 700; margin: 24px 0 0; letter-spacing: -0.5px;">Reset Your Password</h1>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 32px 40px 40px;">
                            <p style="color: #9CA3AF; font-size: 16px; line-height: 24px; margin: 0 0 24px; text-align: center;">
                                We received a request to reset the password for your UV Clinic Appointment System account. You can reset your password by clicking the secure link below.
                            </p>

                            <!-- CTA Button -->
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 32px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}" style="display: inline-block; background-color: #1392EC; color: #FFFFFF; text-decoration: none; font-size: 16px; font-weight: 600; padding: 16px 32px; border-radius: 12px; width: 80%; text-align: center;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #9CA3AF; font-size: 16px; line-height: 24px; margin: 0 0 24px; text-align: center;">
                                This password reset link will expire in 60 minutes.
                            </p>

                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 32px; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 32px;">
                                <tr>
                                    <td>
                                        <p style="color: #6B7280; font-size: 14px; line-height: 20px; margin: 0; text-align: center;">
                                            If you did not request a password reset, no further action is required. Your account remains secure.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding: 24px 40px; background-color: rgba(0, 0, 0, 0.2); border-top: 1px solid rgba(255, 255, 255, 0.05);">
                            <p style="color: #6B7280; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} UV Clinic Appointment System. All rights reserved.
                            </p>
                            <p style="color: #6B7280; font-size: 12px; margin: 8px 0 0; display: block; word-break: break-all;">
                                If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser:<br>
                                <a href="{{ $resetUrl }}" style="color: #1392EC;">{{ $resetUrl }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
