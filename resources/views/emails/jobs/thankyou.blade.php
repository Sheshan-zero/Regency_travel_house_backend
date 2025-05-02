{{-- emails.jobs.thankyou --}}
@component('mail::message')
# Thank You, {{ $application->full_name }}

We’ve received your application for **{{ $application->position_applied }}**.
Our team will review it and get back to you shortly.

Thanks,
Regency Travel House
@endcomponent
