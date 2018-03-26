<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ReservationBuffer;
use App\CarParkSlot;
use App\CarParkSlotDump;
use App\UserPark;
use App\ParkSensor;
use Auth;
use DB;
use App\Transformers\ReservationTransformer;

class ReservationController extends Controller
{
    //add new reservation
    public function addReservation(Request $request, ReservationBuffer $reservation_buffer)
    {
        $this->validate($request,[
            'slot_name' => 'required',
            'arrive_time' => 'required',
            'leaving_time' => 'required',
            'price' => 'required'
        ]);

        $slot_name = $request->slot_name;
        $input = $request->except('slot_name');
        $this->updateStatus($slot_name); // update status car_park_slot

        $slot = CarParkSlot::where('slot_name', $slot_name)
        ->first();

        $old_slot = null;
        $this->updateSensor($slot, $old_slot); // update sensor status

        $this->createCarParkSlotDumps($slot, $slot_name); 

        $reservation_buffer = $reservation_buffer->create([
            'id_user' => Auth::user()->id_user,
            'id_slot' => $slot['id_slot'],
            'validity_limit' => now(),
        ]);

        $this->createReservationTime($input); // create reservation time, input arrive_time, leaving_time, and price

        $check_sensor = CarParkSlot::where('slot_name', $slot_name)->select('id_sensor')->first();

        if($check_sensor['id_sensor'] == null){
            return response()->json('Your slot have not registered yet');
        }

        return fractal()
        ->item($reservation_buffer)
        ->transformWith(new ReservationTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // update status car_park_slot
    private function updateStatus($slot_name)
    {
        $car_park_slot = CarParkSlot::UpdateOrCreate(
            ['slot_name' =>$slot_name],
            ['status' => 'OCCUPIED']
        );

        return $car_park_slot;
    }

    // update status park_sensor
    private function updateSensor($slot, $old_slot)
    {
        $park_sensor = ParkSensor::where('id_sensor',$old_slot['id_sensor'])->update(
            ['status' => 1]
        );

        $park_sensor = ParkSensor::where('id_sensor',$slot['id_sensor'])->update(
            ['status' => 2]
        );

        return $park_sensor;
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
    private function createCarParkSlotDumps($slot, $slot_name)
    {
        $car_park_slot_dump = CarParkSlotDump::create(
            [
                'id_slot' => $slot['id_slot'],
                'id_sensor' => $slot['id_sensor'],
                'status'  => 'OCCUPIED',
                'slot_name' => $slot_name,
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
            'slot_name' => 'required',
            'arrive_time' => 'required',
            'leaving_time' => 'required',
            'price' => 'required'
            ];

        $input = [
            'slot_name' => $request['slot_name'],
            'arrive_time' => $request['arrive_time'],
            'leaving_time' => $request['leaving_time'],
            'price' => $request['price'],
        ];

        $slot_name = $request->slot_name;
        $update = $request->except('slot_name');
        
        $old_slot_status = CarParkSlot::where('id_slot',$reservation_buffer['id_slot'])
        ->update(['status'=>'AVAILABLE']); // update old status car_park_slot before input update

        $this->updateStatus($slot_name); // update status car_park_slot
        
        $old_slot = CarParkSlot::where('id_slot',$reservation_buffer['id_slot'])->first();

        $slot = CarParkSlot::where('slot_name', $slot_name)
        ->first();

        $this->updateSensor($slot, $old_slot); // update sensor status

        $this->createCarParkSlotDumps($slot, $slot_name);

        $reservation_buffer = $reservation_buffer->where('id_reservation', $id_reservation)->update([
            'id_slot' => $slot['id_slot'],
        ]);

        $this->validate($request, $constraints);

        $this->updateReservationTime($update, $id_reservation); // update reservation time 

        $editreservation = ReservationBuffer::findOrFail($id_reservation);
        
        return fractal()
        ->item($editreservation)
        ->transformWith(new ReservationTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // update table car_park_slot_dump gadipake
    /*private function updateCarParkSlotDumps($slot, $slot_name)
    {
            $car_park_slot_dump = CarParkSlotDump::leftJoin('reservation_buffers', 'car_park_slot_dumps.created_at', '=', 'reservation_buffers.validity_limit')
            ->whereColumn('car_park_slot_dumps.created_at','reservation_buffers.validity_limit')
            ->update(
                [
                    'car_park_slot_dumps.id_slot' => $slot,
                    'status'  => 'OCCUPIED',
                    'slot_name' => $slot_name,
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
