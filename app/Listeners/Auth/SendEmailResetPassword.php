<?php

namespace App\Listeners\Auth;

use App\Events\Auth\EmailResetPassword;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use App\Mail\Auth\ResetPassword;

class SendEmailResetPassword
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EmailResetPassword  $event
     * @return void
     */
    public function handle(EmailResetPassword $event)
    {

        Mail::to($event->user->email)->send(new ResetPassword($event->user));
    }
}
