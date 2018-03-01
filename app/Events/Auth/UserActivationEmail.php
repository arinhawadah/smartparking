<?php

namespace App\Events\Auth;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use App\UserRegistration;

class UserActivationEmail
{
    use Dispatchable, SerializesModels;

    public $user_registration;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(UserRegistration $user_registration)
    {
        $this->user_registration = $user_registration;
    }

}
