@component('mail::message')
# Profile Update Required

Hello {{ $user->name }},

{{ $message }}

@component('mail::button', ['url' => route('profile.edit')])
Update Profile
@endcomponent

---

**Analyst Saferwealth**  
Sean Cavanagh, Founder & CEO  
416-545-9559  
[info@saferwealth.com](mailto:info@saferwealth.com)

[Unsubscribe from emails]({{ route('email.unsubscribe', $user->email_unsubscribe_token) }})

@endcomponent
