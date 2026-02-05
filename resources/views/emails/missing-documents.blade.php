@component('mail::message')
# Missing Documents Reminder

Hello {{ $user->name }},

We noticed that your profile is missing the following required documents:

@foreach ($missingDocuments as $doc)
- **{{ $doc['doc_type'] }}**: {{ $doc['description'] }}
@endforeach

Please upload these documents to complete your profile and proceed with the next steps.

@component('mail::button', ['url' => route('dashboard')])
Upload Documents
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
