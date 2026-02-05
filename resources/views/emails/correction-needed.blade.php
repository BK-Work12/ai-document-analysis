@component('mail::message')
# Document Correction Required

Hello {{ $user->name }},

Your **{{ $docType }}** document requires correction. Please review the feedback below and resubmit the document.

@component('mail::panel')
## Feedback

{{ $feedback }}
@endcomponent

@component('mail::button', ['url' => route('dashboard')])
Resubmit Document
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
