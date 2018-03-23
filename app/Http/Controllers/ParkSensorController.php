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
    // buat create tabel sensor->update table car_park_slot dan create car_park_slot_dumps
    public function addSensor(Request $request, ParkSensor $park_sensor)
    {
        $this->validate($request, [
            'id_sensor' => 'required',
            'status'=> 'required',
        ]);
    
        $park_sensor = $park_sensor->UpdateOrCreate(
            ['id_sensor' => $request->id_sensor],
            ['status' => $request->status,
            'time' => now()]
        );

        $this->updateStatus($park_sensor);

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
        else{
            $park_sensor = CarParkSlot::where('id_sensor',$park_sensor['id_sensor'])->update(
                ['status' => 'OCCUPIED']
              );
        }
        return;
    }

    // create table car_park_slot_dump
    private function createCarParkSlotDumps($slot)
    {
        $car_park_slot_dump = CarParkSlotDump::create(
            [
                'id_slot' => $slot['id_slot'],
                'id_sensor' => $slot['id_sensor'],
                'status'  => $slot['status'],
                'slot_name' => $slot['slot_name'],
            ]
        );

        return $car_park_slot_dump;
    }

}
