<?php

namespace App\Transformers;

use App\ParkSensor;
use League\Fractal\TransformerAbstract;

class ParkSensorTransformer extends TransformerAbstract
{
    public function transform(ParkSensor $park_sensor)
    {
        return[
            'id_sensor'=>$park_sensor->id_sensor,
            'status' => $park_sensor->status,
        ];
    } 
    
}