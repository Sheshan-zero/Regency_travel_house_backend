@component('mail::message')
# New Job Application Received

You have received a new job application.

**Name:** {{ $application->full_name }}
**Email:** {{ $application->email }}
**Phone:** {{ $application->phone ?? 'N/A' }}
**Position Applied:** {{ $application->position_applied }}

**Cover Letter:**
{{ $application->cover_letter ?? 'No cover letter provided.' }}

The applicant's CV is attached.

Thanks,
{{ config('app.name') }}
@endcomponent
