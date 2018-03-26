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
    // get all park slot
    public function status(CarParkSlot $car_park_slot)
    {
        $car_park_slot = $car_park_slot->all();

        return fractal()
        ->collection($car_park_slot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // get first available slot
    public function statusAvailableSlot(UserPark $user_park, $arrive_time, $leaving_time)
    {
        $user_park = $user_park
        ->whereDate('arrive_time', date('Y-m-d'))
        ->whereDate('leaving_time', date('Y-m-d'))
        ->whereNotBetween('arrive_time', [date('Y-m-d').' '.$arrive_time, date('Y-m-d').' '.$leaving_time])
        ->whereNotBetween('leaving_time',[date('Y-m-d').' '.$arrive_time, date('Y-m-d').' '.$leaving_time])
        ->take(1)
        ->get();

        return fractal()
        ->collection($user_park)
        ->transformWith(new UserParkTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // get status by time arrive
    public function statusByTime(UserPark $user_park, $time)
    {
        $user_park = $user_park
        ->whereTime('arrive_time','=', $time)
        ->orWhereTime('leaving_time','=', $time)
        ->get();

        return fractal()
        ->collection($user_park)
        ->transformWith(new UserParkTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // create new slot
    public function createParkSlot(Request $request, CarParkSlot $car_park_slot)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $this->validate($request, [
            'status' => 'required',
            'slot_name' => 'required|unique:car_park_slots',
            'id_sensor' => 'required|exists:park_sensors,id_sensor'
        ]);

        $car_park_slot = $car_park_slot->create([
          'status' => $request->status,
          'slot_name' => $request->slot_name,
          'id_sensor' => $request->id_sensor
        ]);

        $slot_name = $request->slot_name;
        $input = $request->all();

        $slot = CarParkSlot::where('slot_name', $input['slot_name'])
        ->pluck('id_slot')
        ->first(); 

        
        $this->createCarParkSlotDumps($slot, $input, $slot_name); // create new entry data car_park_slot_dumps

        return fractal()
        ->item($car_park_slot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // create table car_park_slot_dump
    private function createCarParkSlotDumps($slot, $input, $slot_name)
    {
        $car_park_slot_dump = CarParkSlotDump::create(
            [
                'id_slot' => $slot,
                'id_sensor' => $input['id_sensor'],
                'status'  => $input['status'],
                'slot_name' => $slot_name,
            ]
        );

        return $car_park_slot_dump;
    }

    // update status car_park_slot
    public function updateParkSlot(Request $request, CarParkSlot $car_park_slot, $slot_name)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $car_park_slot = CarParkSlot::where($slot_name);
        
        $constraints = [
            'status' => 'required',
            'id_sensor' => 'required|exists:park_sensors,id_sensor',
            ];

        $input = [
            'status' => $request['status'],
            'id_sensor' => $request['id_sensor'],
        ];

        $slot = CarParkSlot::where('slot_name', $slot_name)
        ->pluck('id_slot')
        ->first();

        $this->createCarParkSlotDumps($slot, $input, $slot_name); // create new entry data car_park_slot_dumps

        $this->validate($request, $constraints);

        CarParkSlot::where('slot_name', $slot_name)->update($input);
        $editslot = CarParkSlot::findOrFail($slot);

        return fractal()
        ->item($editslot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // delete car_park_slot
    public function deleteParkSlot(Request $request, $slot_name)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        CarParkSlotDump::where('slot_name', $slot_name)->delete();
        CarParkSlot::where('slot_name', $slot_name)->delete();

        return response()->json('Delete Success');
    }

}
