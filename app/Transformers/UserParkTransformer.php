<?php

namespace App\Transformers;

use App\UserPark;
use App\CarParkSlot;
use League\Fractal\TransformerAbstract;

class UserParkTransformer extends TransformerAbstract
{
    public function transform(UserPark $user_park)
    {
        $car_park_slot = CarParkSlot::where('id_slot', $user_park->id_slot)->firstOrFail();

        return[
            'id_slot' => $user_park->id_slot,
            'coordinate' => $car_park_slot->coordinate,
        ];
    } 
    
}