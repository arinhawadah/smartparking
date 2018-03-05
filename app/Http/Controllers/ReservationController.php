<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ReservationBuffer;
use App\CarParkSlot;
use App\CarParkSlotDump;
use App\UserPark;
use Auth;
use DB;
use App\Transformers\ReservationTransformer;

class ReservationController extends Controller
{
    public function add(Request $request, ReservationBuffer $reservation_buffer)
    {
        $this->validate($request,[
            'coordinate' => 'required',
            'arrive_time' => 'required',
            'leaving_time' => 'required',
            'price' => 'required'
        ]);

        // update status car_park_slot and update reservation time input coordinate, input reservation time
        $coordinate = $request->coordinate;
        // $arrivetime = $request->arrive_time;
        // $leavingtime = $request->leaving_time;
        // $price = $request->price;
        $input = $request->except('coordinate');
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

        $this->createReservationTime($input);

        return fractal()
        ->item($reservation_buffer)
        ->transformWith(new ReservationTransformer)
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

    // create table user_parks
    private function createReservationTime($input)
    {
        $reservation_buffer = ReservationBuffer::where('id_user', Auth::user()->id_user)->where('validity_limit', now())->firstOrFail();

        $user_park = DB::table('user_parks')->insert(
            array(
                'id_user' => Auth::user()->id_user,
                'id_slot' => $reservation_buffer->id_slot,
                'unique_id' => Auth::user()->unique_id, 
                'arrive_time' => date('Y-m-d').' '.$input['arrive_time'],
                'leaving_time' => date('Y-m-d').' '.$input['leaving_time'],
                'price' => $input['price'],
                'id_reservation'=> $reservation_buffer->id_reservation,
            )
        );

        return $user_park;
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
