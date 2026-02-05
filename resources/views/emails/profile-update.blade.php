@component('mail::message')
# Profile Update Required

Hello {{ $user->name }},

{{ $message }}

@component('mail::button', ['url' => route('profile.edit')])
Update Profile
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
