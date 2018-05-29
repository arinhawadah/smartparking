<?php

namespace App\Transformers;

use App\ParkSensorResp;
use League\Fractal\TransformerAbstract;

class ParkSensorRespTransformer extends TransformerAbstract
{
    public function transform(ParkSensorResp $park_sensor_resp)
    {
        return[
            'id_sensor'=>$park_sensor_resp->id_sensor,
            'status' => $park_sensor_resp->status,
        ];
    } 
    
}