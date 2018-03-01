<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserCredentialTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return[
            'id user' =>$user->id_user,
            'name' => $user->name,
            'email' => $user->email,
            'car_type' => $user->car_type,
            'license_plate_number' => $user->license_plate_number,
            'activate'=> $user->created_at->diffForHumans(),
        ];
    }
}