<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CarParkSlot;
use App\CarParkSlotDump;
use App\UserPark;
use App\Transformers\CarParkSlotTransformer;
use App\Transformers\UserParkTransformer;

class CarParkSlotController extends Controller
{
    public function status(CarParkSlot $car_park_slot)
    {
        $car_park_slot = $car_park_slot->all();

        return fractal()
        ->collection($car_park_slot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    public function statusByTime(UserPark $user_park, $arrive_time, $leaving_time)
    {
        $user_park = $user_park
        ->whereBetween('arrive_time', [date('Y-m-d').' '.$arrive_time, date('Y-m-d').' '.$leaving_time])
        ->whereBetween('leaving_time',[date('Y-m-d').' '.$arrive_time, date('Y-m-d').' '.$leaving_time])
        ->get();

        return fractal()
        ->collection($user_park)
        ->transformWith(new UserParkTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    public function createParkSlot(Request $request, CarParkSlot $car_park_slot)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $this->validate($request, [
            'status' => 'required',
            'coordinate' => 'required|unique:car_park_slots',
            'id_sensor' => 'required'
        ]);

        $car_park_slot = $car_park_slot->create([
          'status' => $request->status,
          'coordinate' => $request->coordinate,
          'id_sensor' => $request->id_sensor
        ]);

        $coordinate = $request->coordinate;
        $input = $request->all();

        $slot = CarParkSlot::where('coordinate', $input['coordinate'])
        ->pluck('id_slot')
        ->first(); 

        $this->createCarParkSlotDumps($slot, $input, $coordinate);

        return fractal()
        ->item($car_park_slot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // create table car_park_slot_dump
    private function createCarParkSlotDumps($slot, $input, $coordinate)
    {
        $car_park_slot_dump = CarParkSlotDump::create(
            [
                'id_slot' => $slot,
                'id_sensor' => $input['id_sensor'],
                'status'  => $input['status'],
                'coordinate' => $coordinate,
            ]
        );

        return $car_park_slot_dump;
    }

    // update status car_park_slot
    public function updateParkSlot(Request $request, CarParkSlot $car_park_slot, $coordinate)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $car_park_slot = CarParkSlot::where($coordinate);
        
        $constraints = [
            'status' => 'required',
            'id_sensor' => 'required',
            ];

        $input = [
            'status' => $request['status'],
            'id_sensor' => $request['id_sensor'],
        ];

        $slot = CarParkSlot::where('coordinate', $coordinate)
        ->pluck('id_slot')
        ->first();

        $this->createCarParkSlotDumps($slot, $input, $coordinate);

        $this->validate($request, $constraints);

        CarParkSlot::where('coordinate', $coordinate)->update($input);
        $editslot = CarParkSlot::findOrFail($slot);

        return fractal()
        ->item($editslot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // delete car_park_slot
    public function deleteParkSlot(Request $request, $coordinate)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        CarParkSlotDump::where('coordinate', $coordinate)->delete();
        CarParkSlot::where('coordinate', $coordinate)->delete();

        return response()->json('Delete Success');
    }

}
