<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0F0F0F; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #0F0F0F; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="max-w-md w-full mx-auto max-width: 600px; background-color: #141414; border-radius: 16px; overflow: hidden; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);">
                    <tr>
                        <td align="center" style="padding: 40px 40px 0;">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="background-color: #1392EC; padding: 12px; border-radius: 12px;">
                                        <img src="https://fonts.gstatic.com/s/i/short-term/release/materialsymbolsoutlined/mark_email_read/default/48px.svg" width="32" height="32" style="display: block; filter: invert(1);" alt="Email Verification">
                                    </td>
                                </tr>
                            </table>
                            <h1 style="color: #FFFFFF; font-size: 24px; font-weight: 700; margin: 24px 0 0; letter-spacing: -0.5px;">Verify Your Email</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px 40px 40px;">
                            <p style="color: #9CA3AF; font-size: 16px; line-height: 24px; margin: 0 0 24px; text-align: center;">
                                Use the verification code below to continue setting up your UV Clinic Appointment System account.
                            </p>

                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 32px 0;">
                                <tr>
                                    <td align="center" style="background-color: rgba(19, 146, 236, 0.08); border: 1px solid rgba(19, 146, 236, 0.2); border-radius: 14px; padding: 22px 16px;">
                                        <p style="color: #6B7280; font-size: 12px; font-weight: 600; letter-spacing: 1.5px; text-transform: uppercase; margin: 0 0 12px;">
                                            Verification Code
                                        </p>
                                        <p style="color: #1392EC; font-size: 34px; font-weight: 700; letter-spacing: 8px; margin: 0; text-align: center;">
                                            {{ $otp }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #9CA3AF; font-size: 16px; line-height: 24px; margin: 0 0 24px; text-align: center;">
                                This code will expire in 5 minutes.
                            </p>

                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-top: 32px; border-top: 1px solid rgba(255, 255, 255, 0.05); padding-top: 32px;">
                                <tr>
                                    <td>
                                        <p style="color: #6B7280; font-size: 14px; line-height: 20px; margin: 0; text-align: center;">
                                            If you did not request this verification, please ignore this email.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 24px 40px; background-color: rgba(0, 0, 0, 0.2); border-top: 1px solid rgba(255, 255, 255, 0.05);">
                            <p style="color: #6B7280; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} UV Clinic Appointment System. All rights reserved.
                            </p>
                            <p style="color: #6B7280; font-size: 12px; margin: 8px 0 0;">
                                This is an automated verification message from UV Clinic Appointment System.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
