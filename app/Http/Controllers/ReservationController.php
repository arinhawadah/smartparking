<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CarParkSlot;
use App\UserPark;
use App\ParkSensor;
use Auth;
use JWTAuth;
use DB;
use App\Transformers\ReservationTransformer;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['addReservation']]);
    }

    //add new reservation
    public function addReservation(Request $request, UserPark $user_park)
    {
        $this->validate($request,[
            'id_slot' => 'required',
            'arrive_time' => 'required',
            'leaving_time' => 'required',
            'price' => 'required'
        ]);

        $id_slot = $request->id_slot;
        // $input = $request->except('id_slot');
        // $this->updateStatus($id_slot); // update status car_park_slot

        // $slot = CarParkSlot::where('id_slot', $id_slot)
        // ->first();

        // // $old_slot = null;
        // // $this->updateSensor($slot, $old_slot); // update sensor status

        // $this->createCarParkSlotDumps($slot); 
            
        $user_park = $user_park->create([
            'id_user' => JWTAuth::parseToken()->authenticate()->id_user,
            'id_slot' => $id_slot,
            'unique_id' => Auth::user()->unique_id, 
            'arrive_time' => date('Y-m-d').' '.$request->arrive_time,
            'leaving_time' => date('Y-m-d').' '.$request->leaving_time,
            'price' => $request->price,
        ]);

        $check_sensor = CarParkSlot::where('id_slot', $id_slot)->select('id_sensor')->first();

        if($check_sensor['id_sensor'] == null){
            return response()->json('Your slot have not registered yet');
        }

        // $this->createReservationTime($input); // create reservation time, input arrive_time, leaving_time, and price

        return fractal()
        ->item($user_park)
        ->transformWith(new ReservationTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

    // update status car_park_slot
    private function updateStatus($id_slot)
    {
        $car_park_slot = CarParkSlot::UpdateOrCreate(
            ['id_slot' =>$id_slot],
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
    // private function createReservationTime($input)
    // {
    //     $reservation_buffer = ReservationBuffer::where('id_user', JWTAuth::parseToken()->authenticate()->id_user)->where('validity_limit', now())->firstOrFail();

    //     $user_park = DB::table('user_parks')->insert(
    //         array(
    //             'id_user' => JWTAuth::parseToken()->authenticate()->id_user,
    //             'id_slot' => $reservation_buffer->id_slot,
    //             'unique_id' => Auth::user()->unique_id, 
    //             'arrive_time' => date('Y-m-d').' '.$input['arrive_time'],
    //             'leaving_time' => date('Y-m-d').' '.$input['leaving_time'],
    //             'price' => $input['price'],
    //             'id_reservation'=> $reservation_buffer->id_reservation,
    //         )
    //     );

    //     return $user_park;
    // }

    // update reservation
    public function updateReservation(Request $request, UserPark $user_park, $id_user_park)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $user_park = UserPark::findOrFail($id_user_park);

        $constraints = [
            'id_slot' => 'required',
            'arrive_time' => 'required',
            'leaving_time' => 'required',
            'price' => 'required'
            ];

        $input = [
            'id_slot' => $request['id_slot'],
            'arrive_time' => $request['arrive_time'],
            'leaving_time' => $request['leaving_time'],
            'price' => $request['price'],
        ];

        $id_slot = $request->id_slot;
        $update = $request->except('id_slot');
        
        // $old_slot_status = CarParkSlot::where('id_slot',$user_park['id_slot'])
        // ->update(['status'=>'AVAILABLE']); // update old status car_park_slot before input update
        
        // $this->updateStatus($id_slot); // update status car_park_slot
        
        // $old_slot = CarParkSlot::where('id_slot',$user_park['id_slot'])->first();

        // $slot = CarParkSlot::where('id_slot', $id_slot)
        // ->first();

        // $this->updateSensor($slot, $old_slot); // update sensor status

        // $this->createCarParkSlotDumps($slot);

        // $reservation_buffer = $reservation_buffer->where('id_reservation', $id_reservation)->update([
        //     'id_slot' => $slot['id_slot'],
        // ]);

        $this->validate($request, $constraints);

        // $this->updateReservationTime($update, $id_reservation); // update reservation time 
        $user_park = DB::table('user_parks')->where('id_user_park', $id_user_park)
        ->update(
            array(
                'id_slot' => $id_slot,
                'arrive_time' => date('Y-m-d').' '.$update['arrive_time'],
                'leaving_time' => date('Y-m-d').' '.$update['leaving_time'],
                'price' => $update['price'],
            )
        );

        $editreservation = UserPark::findOrFail($id_user_park);
        
        return fractal()
        ->item($editreservation)
        ->transformWith(new ReservationTransformer)
        ->toArray();

        return response()->json($response, 201);
    }

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
    public function deleteReservation(Request $request, UserPark $user_park, $id_user_park)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        UserPark::where('id_user_park', $id_user_park)->delete();

        return response()->json('Delete Success');
    }
}
