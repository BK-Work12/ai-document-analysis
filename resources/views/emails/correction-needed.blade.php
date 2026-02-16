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

---

**Analyst Saferwealth**  
Sean Cavanagh, Founder & CEO  
416-545-9559  
[info@saferwealth.com](mailto:info@saferwealth.com)

[Unsubscribe from emails]({{ route('email.unsubscribe', $user->email_unsubscribe_token) }})

@endcomponent
