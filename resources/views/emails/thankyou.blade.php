{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Thank YOu</title>
</head>
<body>

# Thank You for Applying

Dear {{ $application->full_name }},

Thank you for applying for the position of *{{ $application->position_applied }}* at Regency Travel.

We’ve received your application and will be in touch if your profile matches our requirements.

Best regards,  
*Regency Travel Team*

</body>
</html> --}}






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You for Applying</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px; color: #333;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px;">
        <tr>
            <td style="background-color: #20c997; color: #ffffff; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">🙏 Thank You for Applying</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin-bottom: 15px;">Dear {{ $application->full_name }},</p>

                <p style="margin-bottom: 15px;">
                    Thank you for applying for the position of <strong>{{ $application->position_applied }}</strong> at Regency Travel.
                </p>

                <p style="margin-bottom: 20px;">
                    We have received your application and our team will review it shortly. If your profile matches our requirements, we will be in touch with you soon.
                </p>

                <p style="margin-top: 40px;">
                    Best regards,<br>
                    <strong>Regency Travel Team</strong>
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f1f1f1; text-align: center; padding: 15px; font-size: 12px; color: #777;">
                © {{ date('Y') }} Regency Travel. All rights reserved.
            </td>
        </tr>
    </table>

</body>
</html>
