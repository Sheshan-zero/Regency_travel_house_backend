{{-- <!DOCTYPE html>
<html>
<head>
    <title>New Contact Message</title>
</head>
<body>
    <h2>New Contact Message Received</h2>
    <p><strong>From:</strong> {{ $full_name }}  ({{ $email }})</p>
    <p><strong>Subject:</strong> {{ $subjectText }}</p>
    <p><strong>Message:</strong></p>
    <p>{{ $messageText }}</p>
</body>
</html> --}}



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Contact Message</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 40px; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="background-color: #007BFF; color: #fff; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">📬 New Contact Message</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin: 0 0 10px;"><strong>From:</strong> {{ $full_name }} (<a href="mailto:{{ $email }}" style="color: #007BFF;">{{ $email }}</a>)</p>
                <p style="margin: 0 0 10px;"><strong>Subject:</strong> {{ $subjectText }}</p>
                <p style="margin: 20px 0 5px;"><strong>Message:</strong></p>
                <p style="margin: 0; line-height: 1.6;">{{ $messageText }}</p>
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
