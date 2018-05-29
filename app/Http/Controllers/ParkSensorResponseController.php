<?php

namespace App\Http\Controllers;
use App\ParkSensor;
use App\ParkSensorResp;
use App\CarParkSlot;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\ParkSensorRespTransformer;

class ParkSensorResponseController extends Controller
{
    // add new sensor or update sensor status
    public function addSensor(Request $request, ParkSensorResp $park_sensor_resp, $id_sensor, $status)
    {
        // $this->validate($request, [
        //     'id_sensor' => 'required|unique:park_sensors',
        //     'status'=> 'required',
        // ]);
        $park_sensor_resp = $park_sensor_resp->UpdateOrCreate(
            ['id_sensor' => $id_sensor],
            ['status' => $status,
            'time' => now()]
        );

        $status_sensor = ParkSensor::where('id_sensor','=', $park_sensor_resp['id_sensor'])->pluck('status')->first();
            
        $this->updateStatusSensor($status_sensor, $park_sensor_resp); // update status park_sensor_resp
        $this->updateStatusSlot($park_sensor_resp, $status_sensor); // update status car_park_slot
       
        return fractal()
        ->item($park_sensor_resp)
        ->transformWith(new ParkSensorRespTransformer)
        ->toArray();
    
        return response()->json($response, 201);
    }

    // update status car_park_slot
    private function updateStatusSlot($park_sensor_resp, $status_sensor)
    {
        if($park_sensor_resp['status'] == 1 && $status_sensor != '2'){
            $car_park_slot = CarParkSlot::where('id_sensor',$park_sensor_resp['id_sensor'])->update(
                ['status' => 'AVAILABLE']
              );
        }  
        elseif($park_sensor_resp['status'] == '3'){
            $park_sensor_resp = CarParkSlot::where('id_sensor',$park_sensor_resp['id_sensor'])->update(
                ['status' => 'PARKED']
              );
        }
        return;
    }

    // update status car_park_slot
    private function updateStatusSensor($status_sensor, $park_sensor_resp)
    {
        if($park_sensor_resp['status'] == '1' && $status_sensor != '2'){
            ParkSensor::UpdateOrCreate(
                ['id_sensor' => $park_sensor_resp['id_sensor']],
                ['status' => $park_sensor_resp['status'],
                'time' => now()]
            );
        }
        elseif($park_sensor_resp['status'] == '3')
        {
            ParkSensor::UpdateOrCreate(
                ['id_sensor' => $park_sensor_resp['id_sensor']],
                ['status' => $park_sensor_resp['status'],
                'time' => now()]
            );
        }
        return;
    }

}
