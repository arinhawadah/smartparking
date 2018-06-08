<?php

namespace App\Transformers;

use App\ParkSensorResponse;
use League\Fractal\TransformerAbstract;

class ParkSensorResponseTransformer extends TransformerAbstract
{
    public function transform(ParkSensorResponse $park_sensor_response)
    {
        return[
            'id_sensor'=>$park_sensor_response->id_sensor,
            'status' => $park_sensor_response->status,
        ];
    } 
    
}