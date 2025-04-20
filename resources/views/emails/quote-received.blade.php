<!DOCTYPE html>
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
</html>
