<?php

namespace App\Transformers;

use App\UserPark;
use League\Fractal\TransformerAbstract;

class UserParkTimeTransformer extends TransformerAbstract
{
    public function transform(UserPark $user_park)
    {
        return[
            'id_slot' => $user_park->id_slot,
            'day' => date('l', strtotime($user_park->arrive_time)),
        ];
    } 
    
}