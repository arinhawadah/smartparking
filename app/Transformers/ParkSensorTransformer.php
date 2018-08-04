<?php

namespace App\Transformers;

use App\ParkSensor;
use League\Fractal\TransformerAbstract;

class ParkSensorTransformer extends TransformerAbstract
{
    public function transform(ParkSensor $park_sensor)
    {
        return[
            'entry' =>$park_sensor->entry,
            'id_sensor'=>$park_sensor->id_sensor,
            'time'=>$park_sensor->time,
            'status' => $park_sensor->status,
        ];
    } 
    
}