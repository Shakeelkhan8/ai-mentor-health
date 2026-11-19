<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="x-apple-disable-message-reformatting">
    <title>{{ config('mentor.name') }}</title>
</head>
<body style="margin:0;padding:0;background:#f3f5f4;font-family:-apple-system,'Segoe UI',Helvetica,Arial,sans-serif;color:#0b1412;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f5f4;">
        <tr>
            <td align="center" style="padding:40px 16px;">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;">
                    <tr>
                        <td style="background:#0b1412;padding:28px 32px;">
                            <span style="font-size:18px;font-weight:600;color:#ffffff;">AI Mentor <span style="color:#6ee7bb;">Health</span></span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <h1 style="margin:0 0 12px;font-size:22px;line-height:1.3;">Thanks for subscribing</h1>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#3f4a47;">
                                You'll get short, practical tips on sleep, stress, nutrition and fitness from {{ config('mentor.name') }}.
                            </p>
                            <p style="margin:0 0 28px;font-size:15px;line-height:1.6;color:#3f4a47;">
                                Want coaching built around your own goal? Create a free account to talk to the AI coach, check in on daily habits and get a weekly plan.
                            </p>
                            <a href="{{ route('register') }}" style="display:inline-block;background:#059669;color:#ffffff;text-decoration:none;font-size:15px;font-weight:600;padding:12px 22px;border-radius:10px;">Create a free account</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px;border-top:1px solid #e6ebe9;font-size:12px;line-height:1.6;color:#7a8582;">
                            {{ config('mentor.disclaimer') }}<br>
                            If you didn't sign up, you can ignore this email.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
