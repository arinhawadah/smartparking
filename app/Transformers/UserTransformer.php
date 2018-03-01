<?php

namespace App\Transformers;

use App\UserRegistration;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(UserRegistration $user_registration)
    {
        return[
            'id user' =>$user_registration->id,
            'name' => $user_registration->name,
            'email' => $user_registration->email,
            'car_type' => $user_registration->car_type,
            'license_plate_number' => $user_registration->license_plate_number,
            'registered'=> $user_registration->created_at->diffForHumans(),
        ];
    }
}