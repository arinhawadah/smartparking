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
            'slot_name'=>$car_park_slot->slot_name,
            'id_sensor' =>$car_park_slot->id_sensor,
        ];
    } 
    
}