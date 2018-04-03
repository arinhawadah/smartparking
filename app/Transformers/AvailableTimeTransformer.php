<?php

namespace App\Transformers;

use App\CarParkSlot;
use League\Fractal\TransformerAbstract;

class AvailableTimeTransformer extends TransformerAbstract
{
    public function transform(CarParkSlot $car_park_slot)
    {
        return[
            'id_slot' => $car_park_slot->id_slot,
            'slot_name' => $car_park_slot->slot_name,
        ];
    } 
    
}