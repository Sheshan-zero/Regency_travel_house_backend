@component('mail::message')
# Payment Receipt Uploaded

A customer has uploaded a payment receipt.

**Customer:** {{ $booking->customer->full_name ?? 'N/A' }}
**Package:** {{ $booking->package->title ?? 'N/A' }}
**Booking ID:** {{ $booking->id }}
**Travel Date:** {{ $booking->travel_date }}

The receipt is attached to this email.

Thanks,
{{ config('app.name') }}
@endcomponent
