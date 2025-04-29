{{-- <!DOCTYPE html>
<html>
<head>
    <title>Your Quote is Ready</title>
</head>
<body>
    <h2>Hello {{ $quote->customer->full_name }},</h2>

    <p>We’ve reviewed your travel quote request for the package: <strong>{{ $quote->package->title }}</strong>.</p>

    <p>Your quote is now ready! To view the details and proceed, please log in to your profile.</p>

    <p>
        <a href="http://localhost:5173/transactions" style="display:inline-block; padding:10px 20px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:5px;">
            View Your Quote
        </a>
    </p>

    <p>If you have any questions or need assistance, feel free to contact our support team.</p>

    <br>
    <p>Best regards,<br>Regency Travel Team</p>
</body>
</html> --}}





<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Quote is Ready</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 40px; color: #333;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px;">
        <tr>
            <td style="background-color: #007bff; color: #ffffff; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">🎉 Your Quote is Ready!</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin: 0 0 15px;">Hello {{ $quote->customer->full_name }},</p>

                <p style="margin: 0 0 15px;">
                    We’ve reviewed your travel quote request for the package:
                    <strong>{{ $quote->package->title }}</strong>.
                </p>

                <p style="margin: 0 0 20px;">
                    Your quote is now ready! To view the full details and proceed with your booking, please log in to your profile using the button below.
                </p>

                <p style="margin: 30px 0;">
                    <a href="http://localhost:5173/transactions"
                       style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold;">
                        View Your Quote
                    </a>
                </p>

                <p style="margin: 0 0 15px;">
                    If you have any questions or need assistance, our support team is happy to help!
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
