<?php

namespace App\Http\Controllers;
use App\ParkSensor;
use App\ParkSensorResponse;
use App\CarParkSlot;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\ParkSensorResponseTransformer;

class ParkSensorResponseController extends Controller
{
    // add new sensor or update sensor status
    public function addSensor(Request $request, ParkSensorResponse $park_sensor_response, $id_sensor, $status)
    {
        // $this->validate($request, [
        //     'id_sensor' => 'required|unique:park_sensors',
        //     'status'=> 'required',
        // ]);
        $park_sensor_response = $park_sensor_response->UpdateOrCreate(
            ['id_sensor' => $id_sensor],
            ['status' => $status,
            'time' => now()]
        );

        $status_sensor = ParkSensor::where('id_sensor','=', $park_sensor_response['id_sensor'])->pluck('status')->first();
            
        $this->updateStatusSensor($status_sensor, $park_sensor_response); // update status park_sensor_response
        $this->updateStatusSlot($park_sensor_response, $status_sensor); // update status car_park_slot
       
        return fractal()
        ->item($park_sensor_response)
        ->transformWith(new ParkSensorResponseTransformer)
        ->toArray();
    
        return response()->json($response, 201);
    }

    // update status car_park_slot
    private function updateStatusSlot($park_sensor_response, $status_sensor)
    {
        if($park_sensor_response['status'] == 0 && $status_sensor != '2'){
            $car_park_slot = CarParkSlot::where('id_sensor',$park_sensor_response['id_sensor'])->update(
                ['status' => 'AVAILABLE']
              );
        }  
        elseif($park_sensor_response['status'] == '1'){
            $park_sensor_response = CarParkSlot::where('id_sensor',$park_sensor_response['id_sensor'])->update(
                ['status' => 'PARKED']
              );
        }
        return;
    }

    // update status car_park_slot
    private function updateStatusSensor($status_sensor, $park_sensor_response)
    {
        if($park_sensor_response['status'] == '0' && $status_sensor != '2'){
            ParkSensor::UpdateOrCreate(
                ['id_sensor' => $park_sensor_response['id_sensor']],
                ['status' => $park_sensor_response['status'],
                'time' => now()]
            );
        }
        elseif($park_sensor_response['status'] == '1')
        {
            ParkSensor::UpdateOrCreate(
                ['id_sensor' => $park_sensor_response['id_sensor']],
                ['status' => $park_sensor_response['status'],
                'time' => now()]
            );
        }
        return;
    }

}
