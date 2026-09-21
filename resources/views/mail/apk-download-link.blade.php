<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحميل تطبيق NA Egypt التجريبي</title>
</head>
<body style="margin: 0; padding: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f1f5f9; color: #1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <!-- Header -->
        <tr>
            <td style="background: linear-gradient(135deg, #1e3a5f 0%, #32557f 100%); padding: 32px 24px; text-align: center; color: #ffffff;">
                <h1 style="margin: 0; font-size: 22px; font-weight: 700; letter-spacing: -0.5px;">زمالة المدمنين المجهولين - مصر</h1>
                <p style="margin: 6px 0 0; font-size: 14px; opacity: 0.9;">Narcotics Anonymous Egypt &bull; IT Workgroup</p>
                <div style="display: inline-block; margin-top: 14px; background-color: rgba(255,255,255,0.18); padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                    تطبيق أندرويد &bull; Android APK v1.2.0-Hope
                </div>
            </td>
        </tr>

        <!-- Body Arabic -->
        <tr>
            <td style="padding: 32px 24px 16px; direction: rtl; text-align: right;">
                <h2 style="color: #0f172a; font-size: 18px; margin-top: 0; margin-bottom: 12px;">مرحباً،</h2>
                <p style="font-size: 15px; line-height: 1.7; margin-bottom: 16px;">
                    لقد طلبت رابط تحميل النسخة التجريبية الخاصة بتطبيق <strong>NA Egypt</strong> لأجهزة أندرويد (Android).
                </p>
                <p style="font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 24px;">
                    هذا الرابط مخصص لحسابك وهو صالح لمدة <strong>{{ $expiryHours }} ساعة</strong> من وقت الطلب.
                </p>

                <!-- CTA Button -->
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $downloadUrl }}" target="_blank" rel="noopener noreferrer" style="background-color: #2563eb; color: #ffffff; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-size: 16px; font-weight: 700; display: inline-block; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                        تحميل ملف التطبيق (APK) الآن
                    </a>
                </div>

                <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-top: 20px; font-size: 13px; color: #64748b; line-height: 1.6;">
                    <strong style="color: #334155;">ملاحظات هامة لتثبيت النسخة التجريبية:</strong>
                    <ul style="margin: 8px 0 0; padding-right: 20px;">
                        <li>قد يطلب هاتفك السماح بالتثبيت من مصادر غير معروفة (Install from unknown sources).</li>
                        <li>في حال واجهت أي ملاحظات أو مشاكل أثناء الاختبار، يرجى إفادة لجنة التكنولوجيا (web@naegypt.org).</li>
                    </ul>
                </div>
            </td>
        </tr>

        <!-- Divider -->
        <tr>
            <td style="padding: 0 24px;"><hr style="border: 0; border-top: 1px dashed #cbd5e1; margin: 16px 0;"></td>
        </tr>

        <!-- Body English -->
        <tr>
            <td style="padding: 16px 24px 32px; direction: ltr; text-align: left;">
                <h3 style="color: #0f172a; font-size: 16px; margin-top: 0; margin-bottom: 8px;">Pre-release APK Download Link (English)</h3>
                <p style="font-size: 14px; line-height: 1.6; color: #475569; margin-bottom: 16px;">
                    You requested the pre-release Android APK for the <strong>NA Egypt</strong> mobile app. Your download link is valid for <strong>{{ $expiryHours }} hours</strong>.
                </p>
                <div style="text-align: center; margin: 20px 0;">
                    <a href="{{ $downloadUrl }}" target="_blank" rel="noopener noreferrer" style="background-color: #0284c7; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; display: inline-block;">
                        Download APK File
                    </a>
                </div>
                <p style="font-size: 12px; color: #94a3b8; margin-top: 16px;">
                    If the button doesn't work, copy and paste this link into your browser:<br>
                    <span style="word-break: break-all; color: #2563eb;">{{ $downloadUrl }}</span>
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #f8fafc; padding: 20px 24px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
                &copy; {{ date('Y') }} Narcotics Anonymous Egypt. All rights reserved.<br>
                هذه الرسالة مرسلة تلقائياً، يرجى عدم الرد المباشر على هذا البريد.
            </td>
        </tr>
    </table>
</body>
</html>
