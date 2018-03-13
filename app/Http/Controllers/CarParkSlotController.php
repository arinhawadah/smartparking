<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CarParkSlot;
use App\CarParkSlotDump;
use App\Transformers\CarParkSlotTransformer;

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

    public function createParkSlot(Request $request, CarParkSlot $car_park_slot)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $this->validate($request, [
            'status' => 'required',
            'coordinate' => 'required|unique:car_park_slots',
        ]);

        $car_park_slot = $car_park_slot->create([
          'status' => $request->status,
          'coordinate' => $request->coordinate
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
            ];

        $input = [
            'status' => $request['status'],
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

        return response()->json('Delete Succes');
    }

}
