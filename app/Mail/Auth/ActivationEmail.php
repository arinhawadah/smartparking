<?php

namespace App\Mail\Auth;

use App\UserRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActivationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user_registration;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(UserRegistration $user_registration)
    {
        $this->user_registration = $user_registration;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.auth.activation');
    }
}
