<!DOCTYPE html>
<html>
<head>
    <title>Quote Response</title>
</head>
<body>
    <h2>Hello {{ $quote->customer->full_name }},</h2>

    <p>We’ve reviewed your travel quote request for the package: <strong>{{ $quote->package->title }}</strong>.</p>

    <p><strong>Estimated Price:</strong> {{ $quote->estimated_price }} LKR</p>

    <p>If you have further questions, feel free to reach out. We look forward to helping you plan your journey!</p>

    <br>
    <p>Best regards,<br>Regency Travel Team</p>
</body>
</html>
