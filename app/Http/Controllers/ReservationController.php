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
    public function addReservation(Request $request, ReservationBuffer $reservation_buffer)
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

    // update reservation
    public function updateReservation(Request $request, ReservationBuffer $reservation_buffer, $id_reservation)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $reservation_buffer = ReservationBuffer::findOrFail($id_reservation);
        $constraints = [
            'coordinate' => 'required',
            'arrive_time' => 'required',
            'leaving_time' => 'required',
            'price' => 'required'
            ];

        $input = [
            'coordinate' => $request['coordinate'],
            'arrive_time' => $request['arrive_time'],
            'leaving_time' => $request['leaving_time'],
            'price' => $request['price'],
        ];

        $coordinate = $request->coordinate;
        $update = $request->except('coordinate');
        $this->updateStatus($coordinate);

        $slot = CarParkSlot::where('coordinate', $coordinate)
        ->pluck('id_slot')
        ->first();

        $this->createCarParkSlotDumps($slot, $coordinate);

        $reservation_buffer = $reservation_buffer->where('id_reservation', $id_reservation)->update([
            'id_slot' => $slot,
        ]);

        $this->validate($request, $constraints);

        $this->updateReservationTime($update, $id_reservation);

        $editreservation = ReservationBuffer::findOrFail($id_reservation);
        
        return fractal()
        ->item($editreservation)
        ->transformWith(new ReservationTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // update table car_park_slot_dump gadipake
    /*private function updateCarParkSlotDumps($slot, $coordinate)
    {
            $car_park_slot_dump = CarParkSlotDump::leftJoin('reservation_buffers', 'car_park_slot_dumps.created_at', '=', 'reservation_buffers.validity_limit')
            ->whereColumn('car_park_slot_dumps.created_at','reservation_buffers.validity_limit')
            ->update(
                [
                    'car_park_slot_dumps.id_slot' => $slot,
                    'status'  => 'OCCUPIED',
                    'coordinate' => $coordinate,
                ]
            );
    
            return $car_park_slot_dump;
    }*/

    // update table user_parks
    private function updateReservationTime($update, $id_reservation)
    {
        $reservation_buffer = ReservationBuffer::where('id_reservation', $id_reservation)->firstOrFail();

        $user_park = DB::table('user_parks')->update(
            array(
                'id_slot' => $reservation_buffer->id_slot,
                'arrive_time' => date('Y-m-d').' '.$update['arrive_time'],
                'leaving_time' => date('Y-m-d').' '.$update['leaving_time'],
                'price' => $update['price'],
            )
        );

        return $user_park;
    }

    // delete reservation
    public function deleteReservation(Request $request, ReservationBuffer $reservation_buffer, $id_reservation)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        UserPark::where('id_reservation', $id_reservation)->delete();
        ReservationBuffer::where('id_reservation', $id_reservation)->delete();

        return response()->json('Delete Success');
    }
}
