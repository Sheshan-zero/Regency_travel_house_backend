{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

    #New Job Application
    
    A new application has been submitted.
    
    *Name:* {{ $application->full_name }}  
    *Email:* {{ $application->email }}  
    *Phone:* {{ $application->phone }}  
    *Position Applied:* {{ $application->position_applied }}
    
    The applicant's CV is attached to this email.
    

</body>
</html> --}}





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Job Application</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 40px; color: #333;">

    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px;">
        <tr>
            <td style="background-color: #28a745; color: #ffffff; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">📄 New Job Application</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin-bottom: 20px;">A new application has been submitted. Details are as follows:</p>
                
                <p style="margin: 10px 0;"><strong>Name:</strong> {{ $application->full_name }}</p>
                <p style="margin: 10px 0;"><strong>Email:</strong> <a href="mailto:{{ $application->email }}" style="color: #007bff;">{{ $application->email }}</a></p>
                <p style="margin: 10px 0;"><strong>Phone:</strong> {{ $application->phone }}</p>
                <p style="margin: 10px 0;"><strong>Position Applied:</strong> {{ $application->position_applied }}</p>
                
                <p style="margin-top: 30px;">📎 The applicant's CV is attached to this email.</p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f1f1f1; text-align: center; padding: 15px; font-size: 12px; color: #777;">
                © {{ date('Y') }} Regency Travel Careers. All rights reserved.
            </td>
        </tr>
    </table>

</body>
</html>
