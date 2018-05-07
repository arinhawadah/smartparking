<?php

namespace App\Http\Controllers;
use App\ParkSensor;
use App\CarParkSlot;
use App\CarParkSlotDump;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\ParkSensorTransformer;

class ParkSensorController extends Controller
{
    // add new sensor or update sensor status
    public function addSensor(Request $request, ParkSensor $park_sensor, $id_sensor, $status)
    {
        // $this->validate($request, [
        //     'id_sensor' => 'required|unique:park_sensors',
        //     'status'=> 'required',
        // ]);
    
        $park_sensor = $park_sensor->UpdateOrCreate(
            ['id_sensor' => $id_sensor],
            ['status' => $status,
            'time' => now()]
        );

        $this->updateStatus($park_sensor); // update status car_park_slot

        $slot = CarParkSlot::where('id_sensor', $park_sensor['id_sensor'])
        ->first(); 

        $this->createCarParkSlotDumps($slot);
       
        return fractal()
        ->item($park_sensor)
        ->transformWith(new ParkSensorTransformer)
        ->toArray();
    
        return response()->json($response, 201);
    }

    // update status car_park_slot
    private function updateStatus($park_sensor)
    {
        if($park_sensor['status'] == 1){
            $car_park_slot = CarParkSlot::where('id_sensor',$park_sensor['id_sensor'])->update(
                ['status' => 'AVAILABLE']
              );
        }
        elseif($park_sensor['status'] == 2){
            $car_park_slot = CarParkSlot::where('id_sensor',$park_sensor['id_sensor'])->update(
                ['status' => 'OCCUPIED']
              );
        }        
        else{
            $park_sensor = CarParkSlot::where('id_sensor',$park_sensor['id_sensor'])->update(
                ['status' => 'PARKED']
              );
        }
        return;
    }

    // create table car_park_slot_dump
    private function createCarParkSlotDumps($slot)
    {
        if($slot['id_slot'] != null){
            $car_park_slot_dump = CarParkSlotDump::create(
                [
                    'id_slot' => $slot['id_slot'],
                    'id_sensor' => $slot['id_sensor'],
                    'status'  => $slot['status'],
                    'slot_name' => $slot['slot_name'],
                ]
            );
        }

        return;
    }

    // delete park_sensor
    public function deleteParkSensor(Request $request, $id_sensor)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        CarParkSlotDump::where('id_sensor', $id_sensor)->delete();
        ParkSensor::where('id_sensor', $id_sensor)->delete();

        return response()->json('Delete Success');
    }

    //get status from database
    public function getSensorStatus(ParkSensor $park_sensor, $id_sensor)
    {
        $sensor_status = $park_sensor->where('id_sensor', $id_sensor)
        ->pluck('status')
        ->first();

        return $sensor_status;
    }
}
