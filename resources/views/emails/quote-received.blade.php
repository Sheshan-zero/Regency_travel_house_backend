{{-- <!DOCTYPE html>
<html>
<head>
    <title>Quote Received</title>
</head>
<body>
    <h2>Hello {{ $quote->customer->full_name }},</h2>
    <p>Thank you for requesting a quote for the <strong>{{ $quote->package->title }}</strong> package.</p>
    <p>Our team has received your request and will get back to you soon with the details.</p>
    <p>If you have any urgent questions, feel free to contact us directly.</p>
    <br>
    <p>Best regards,<br>Regency Travel Team</p>
</body>
</html> --}}





<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quote Received</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px; color: #333;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px;">
        <tr>
            <td style="background-color: #17a2b8; color: #ffffff; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">📥 Quote Request Received</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin-bottom: 15px;">Hello {{ $quote->customer->full_name }},</p>

                <p style="margin-bottom: 15px;">
                    Thank you for requesting a quote for the <strong>{{ $quote->package->title }}</strong> travel package.
                </p>

                <p style="margin-bottom: 15px;">
                    Our travel experts have received your request and will get back to you shortly with all the details you need.
                </p>

                <p style="margin-bottom: 20px;">
                    If you have any urgent questions or specific preferences, feel free to reach out to us directly — we’re happy to help!
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
