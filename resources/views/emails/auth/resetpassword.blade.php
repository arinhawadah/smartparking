@component('mail::message')
# Reset Password Parkys

For reset password your Parkys account, Please click button bellow

@component('mail::button', ['url' => route('auth.resetpassword', [
        'email'=>$user->email
        ])
    ]
)
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent