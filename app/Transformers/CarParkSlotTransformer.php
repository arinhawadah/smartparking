<?php

namespace App\Transformers;

use App\CarParkSlot;
use League\Fractal\TransformerAbstract;

class CarParkSlotTransformer extends TransformerAbstract
{
    public function transform(CarParkSlot $car_park_slot)
    {
        return[
            'id_slot' => $car_park_slot->id_slot,
            'status' => $car_park_slot->status,
            'coordinate'=>$car_park_slot->coordinate,
        ];
    } 
    
}