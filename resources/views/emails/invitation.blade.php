@component('mail::message')
# You've been invited!

You have been invited to join **{{ $companyName }}** as **{{ $role }}**.

Click the button below to accept your invitation. The link expires in 7 days.

@component('mail::button', ['url' => $url, 'color' => 'blue'])
Accept Invitation
@endcomponent

If you did not expect this invitation, you can ignore this email.

Thanks,
**SaaS Platform**
@endcomponent