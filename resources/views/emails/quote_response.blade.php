<!DOCTYPE html>
<html>
<head>
    <title>Your Quote is Ready</title>
</head>
<body>
    <h2>Hello {{ $quote->customer->full_name }},</h2>

    <p>We’ve reviewed your travel quote request for the package: <strong>{{ $quote->package->title }}</strong>.</p>

    <p>Your quote is now ready! To view the details and proceed, please log in to your profile.</p>

    <p>
        <a href="#" style="display:inline-block; padding:10px 20px; background-color:#007bff; color:#fff; text-decoration:none; border-radius:5px;">
            View Your Quote
        </a>
    </p>

    <p>If you have any questions or need assistance, feel free to contact our support team.</p>

    <br>
    <p>Best regards,<br>Regency Travel Team</p>
</body>
</html>
