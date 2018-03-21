<?php

namespace App\Http\Controllers;
use App\ParkSensor;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\ParkSensorTransformer;

class ParkSensorController extends Controller
{
    // buat create tabel sensor->update table car_park_slot dan create car_park_slot_dumps
    public function createSensor(Request $request, ParkSensor $park_sensor)
    {
        $this->validate($request, [
            'id_sensor' => 'required',
            'status'=> 'required',
        ]);
    
        $park_sensor = $park_sensor->create([
            'id_sensor' => $request->id_sensor,
            'status' => $request->status,
            'time' => now(),
        ]);
       
        return fractal()
        ->item($park_sensor)
        ->transformWith(new ParkSensorTransformer)
        ->toArray();
    
        return response()->json($response, 201);
    }
}
