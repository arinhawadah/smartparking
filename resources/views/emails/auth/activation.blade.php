@component('mail::message')
# Thank You For Your Registered

For activate your SPark Account, Please click button bellow

@component('mail::button', ['url' => route('auth.activate', [
        'token'=>$user_registration->activation_token,
        'email'=>$user_registration->email
        ])
    ]
)
Activate
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent