@component('mail::message')
# Reset Password SPark

For reset password your SPark account, Please click button bellow

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