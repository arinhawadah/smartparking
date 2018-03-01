<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ReservationBuffer;
use App\CarParkSlot;
use App\CarParkSlotDump;
use Auth;
use App\Transformers\ReservationBufferTransformer;

class ReservationBufferController extends Controller
{
    public function add(Request $request, ReservationBuffer $reservation_buffer)
    {
        $this->validate($request,[
            'coordinate' => 'required',
        ]);

        // update status car_park_slot, input coordinate
        $coordinate = $request->coordinate;
        $this->updateStatus($coordinate);

        $slot = CarParkSlot::where('coordinate', $coordinate)
        ->pluck('id_slot')
        ->first();

        $this->createCarParkSlotDumps($slot, $coordinate);

        $reservation_buffer = $reservation_buffer->create([
            'id_user' => Auth::user()->id_user,
            'id_slot' => $slot,
            'validity_limit' => now(),
        ]);

        return fractal()
        ->item($reservation_buffer)
        ->transformWith(new ReservationBufferTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // update status car_park_slot
    private function updateStatus($coordinate)
    {
        $car_park_slot = CarParkSlot::updateOrCreate(
            ['coordinate' =>$coordinate],
            ['status' => 'OCCUPIED']
        );

        return $car_park_slot;
    }

    // create table car_park_slot_dump
    private function createCarParkSlotDumps($slot, $coordinate)
    {
        $car_park_slot_dump = CarParkSlotDump::create(
            [
                'id_slot' => $slot,
                'status'  => 'OCCUPIED',
                'coordinate' => $coordinate,
            ]
        );

        return $car_park_slot_dump;
    }
}
