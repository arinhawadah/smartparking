<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CarParkSlot;
use App\UserPark;
use App\ParkSensor;
use App\User;
use App\UserBalance;
use App\HistoryTransaction;
use Auth;
use JWTAuth;
use DB;
use App\Role;
use App\Transformers\ReservationTransformer;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['addReservation']]);
    }

    public function create()
    {
        $slot = CarParkSlot::all();
        return view('reservation-mgmt/create', ['slot' => $slot]);
    }

    public function index(Request $request, UserPark $user_park)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        // $users = $user->all();

        $users = $user_park
        ->leftJoin('user_credentials','user_parks.id_user','=','user_credentials.id_user')
        ->leftJoin('car_park_slots','user_parks.id_slot_user_park','=','car_park_slots.id_slot')
        ->whereMonth('arrive_time','=',date('m'))
        ->select('car_park_slots.slot_name','user_parks.arrive_time','user_parks.leaving_time','user_parks.price',
        'user_parks.id_user_park','user_credentials.name')
        ->orderBy('id_user_park', 'desc')
        ->paginate(10);

        // if ($request->wantsJson())
        // {
        // return fractal()
        // ->collection($users)
        // ->transformWith(new UserCredentialTransformer)
        // ->toArray();
        // }

        return view('reservation-mgmt/index', ['users' => $users]);
    }

    //add new reservation user
    public function addReservation(Request $request, UserPark $user_park)
    {
        $this->validate($request,[
            'id_slot' => 'required',
            'arrive_time' => 'required',
            'leaving_time' => 'required',
            'price' => 'required'
        ]);

        $id_slot = $request->id_slot;
        $price = $request->price;

        $check_sensor = CarParkSlot::where('id_slot', $id_slot)->select('id_sensor')->first();

        if($check_sensor['id_sensor'] == null){
            return response()->json('Your slot have not registered yet');
        }

        $old_balance = UserBalance::where('id_user', JWTAuth::parseToken()->authenticate()->id_user)->pluck('balance')->first();

        $new_balance = $old_balance - $price;

        $input = [
            'balance' =>  $new_balance,
            ];

        if($new_balance < 20000)
        {
            return response()->json(['error' => 'Insufficient balance'], 402);
        }

        UserBalance::where('id_user', JWTAuth::parseToken()->authenticate()->id_user)->update($input);
        
        // $this->updateBalance($price);

        $user_park = $user_park->create([
            'id_user' => JWTAuth::parseToken()->authenticate()->id_user,
            'id_slot_user_park' => $request->id_slot,
            'unique_id' => Auth::user()->unique_id, 
            'arrive_time' => date('Y-m-d').' '.$request->arrive_time,
            'leaving_time' => date('Y-m-d').' '.$request->leaving_time,
            'price' => $price,
        ]);

        $this->historyTransaction($user_park);

        return fractal()
        ->item($user_park)
        ->transformWith(new ReservationTransformer)
        ->toArray();
    }

    //add new reservation admin
    public function store(Request $request, UserPark $user_park, User $user)
    {
        $this->validate($request,[
            'email'=> 'required',
            'id_slot' => 'required',
            'arrive_time' => 'required',
            'leaving_time' => 'required',
            'price' => 'required'
        ]);

        $id_slot = $request->id_slot;
        $price = $request->price;
        
        $user = $user->where('email','=', $request->email)->select('id_user','unique_id')->first();

        $check_sensor = CarParkSlot::where('id_slot', $id_slot)->select('id_sensor')->first();

        if($check_sensor['id_sensor'] == null){
            return response()->json('Your slot have not registered yet');
        }

        $old_balance = UserBalance::where('id_user', $user['id_user'])->pluck('balance')->first();

        $new_balance = $old_balance - $price;

        $input = [
            'balance' =>  $new_balance,
            ];

        if($new_balance < 20000)
        {
            return response()->json(['error' => 'Insufficient balance'], 402);
        }

        UserBalance::where('id_user', $user['id_user'])->update($input);

        $user_park = $user_park->create([
            'id_user' => $user->id_user,
            'id_slot_user_park' => $request->id_slot,
            'unique_id' => $user->unique_id, 
            'arrive_time' => $request->arrive_time,
            'leaving_time' => $request->leaving_time,
            'price' => $request->price,
        ]);

        $this->historyTransactionfromAdmin($user_park);
        return redirect()->intended('/reservation-admin');
    }

    // update reservation
    public function updateReservation(Request $request, UserPark $user_park, $id_user_park)
    {
        $user_park = UserPark::findOrFail($id_user_park);

        $constraints = [
            'id_slot' => 'required',
            ];

        $this->validate($request, $constraints);

        // $this->updateReservationTime($update, $id_reservation); // update reservation time 
        $user_park = $user_park->where('id_user_park', $id_user_park)
            ->update(
                [
                    'id_slot_user_park' => $request->id_slot,
                ]
            );

        $editreservation = UserPark::findOrFail($id_user_park);
        
        return fractal()
        ->item($editreservation)
        ->transformWith(new ReservationTransformer)
        ->toArray();
    }

    // update reservation for admin
    public function update(Request $request, UserPark $user_park, $id_user_park)
    {
            $old_user_park = UserPark::findOrFail($id_user_park);
    
            $constraints = [
                'id_slot' => 'required',
                'arrive_time' => 'required',
                'leaving_time' => 'required',
                'price' => 'required'
                ];
    
            $id_slot = $request->id_slot;
            $price = $request->price;
            $update = $request->except('id_slot');
    
            $this->validate($request, $constraints);

            $old_balance = UserBalance::where('id_user', $old_user_park['id_user'])->pluck('balance')->first();

            if($price != $old_user_park['price'])
            {
                $new_balance = ($old_balance + $old_user_park['price']) - $price;
            }
            else
            {
                $new_balance = $old_balance;
            }
    
            $input = [
                'balance' =>  $new_balance,
                ];
    
            if($new_balance < 20000)
            {
                return redirect()->back()->with('errors', 'The given data was invalid.');
            }
    
            UserBalance::where('id_user', $old_user_park['id_user'])->update($input);   
    
            $user_park = $user_park->where('id_user_park', $id_user_park)
            ->update(
                [
                    'id_slot_user_park' => $id_slot,
                    'arrive_time' => $update['arrive_time'],
                    'leaving_time' => $update['leaving_time'],
                    'price' => $update['price'],
                ]
            );
    
            $editreservation = UserPark::findOrFail($id_user_park);
    
            return redirect()->intended('reservation-admin');
    }

    // delete reservation
    public function destroy(Request $request, $id_user_park)
    {
        // $request->user()->authorizeRoles(['Super Admin', 'Admin', 'User']);

        $user_park = UserPark::where('id_user_park', $id_user_park)->select('price','id_user')->first();
        $old_balance = UserBalance::where('id_user', $user_park['id_user'])->pluck('balance')->first();

        $new_balance = [
            'balance' => $old_balance + $user_park['price'],
        ];

        UserBalance::where('id_user', $user_park['id_user'])->update($new_balance);

        UserPark::where('id_user_park', $id_user_park)->delete();
        HistoryTransaction::where('id_user_park', $id_user_park)->delete();

        if ($request->wantsJson()){
        return response()->json('Delete Success');
        }

        return redirect()->intended('reservation-admin');
    }

    //history transaction
    private function historyTransaction($user_park)
    {
        $history_transaction = HistoryTransaction::insert(
            [
                'id_slot' => $user_park['id_slot_user_park'],
                'id_user' => JWTAuth::parseToken()->authenticate()->id_user,
                'id_user_park'  => $user_park['id_user_park'],
                'price' => $user_park['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        return $history_transaction;
    }

    //history transaction
    private function historyTransactionfromAdmin($user_park)
    {
        $history_transaction = HistoryTransaction::insert(
            [
                'id_slot' => $user_park['id_slot_user_park'],
                'id_user' => $user_park['id_user'],
                'id_user_park'  => $user_park['id_user_park'],
                'price' => $user_park['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        return $history_transaction;
    }

    //search reservation by id_user_park
    public function edit(Request $request, $id_user_park)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $user = UserPark::findOrFail($id_user_park);
        $slot = CarParkSlot::all();

        // if ($request->wantsJson())
        // {
        // return fractal()
        // ->item($user)
        // ->transformWith(new UserCredentialTransformer)
        // ->toArray();
        // }
        
        return view('reservation-mgmt/edit', ['user' => $user, 'slot' => $slot]);
    }

    //search reservation by id_user
    public function showReservationbyUser(Request $request, User $user)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $constraints = [
            'name' => $request['name'],
            ];

       $users = $this->doSearchingQuery($constraints);

        return view('reservation-mgmt/index', ['users' => $users, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = UserPark::leftJoin('user_credentials','user_parks.id_user','=','user_credentials.id_user')
        ->leftJoin('car_park_slots','user_parks.id_slot_user_park','=','car_park_slots.id_slot')
        ->whereMonth('arrive_time','=',date('m'))
        ->select('car_park_slots.slot_name','user_parks.arrive_time','user_parks.leaving_time','user_parks.price',
        'user_parks.id_user_park','user_credentials.name')
        ->orderBy('id_user_park', 'desc');
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        $querys = $query->count();
        return $query->paginate($querys);
    }

    // private function updateBalance($price)
    // {
    //     $old_balance = UserBalance::where('id_user', JWTAuth::parseToken()->authenticate()->id_user)->pluck('balance')->first();

    //     $new_balance = $old_balance - $price;

    //     $input = [
    //         'balance' =>  $new_balance,
    //         ];

    //     if($new_balance < 0)
    //     {
    //         return response()->json(['error' => 'Insufficient balance']);
    //     }
    //     UserBalance::where('id_user', JWTAuth::parseToken()->authenticate()->id_user)->update($input);

    //     return;
    // }


    // // update status car_park_slot
    // private function updateStatus($id_slot)
    // {
    //     $car_park_slot = CarParkSlot::UpdateOrCreate(
    //         ['id_slot' =>$id_slot],
    //         ['status' => 'OCCUPIED']
    //     );

    //     return $car_park_slot;
    // }

    // // update status park_sensor
    // private function updateSensor($slot, $old_slot)
    // {
    //     $park_sensor = ParkSensor::where('id_sensor',$old_slot['id_sensor'])->update(
    //         ['status' => 1]
    //     );

    //     $park_sensor = ParkSensor::where('id_sensor',$slot['id_sensor'])->update(
    //         ['status' => 2]
    //     );

    //     return $park_sensor;
    // }

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

    // update table user_parks
    // private function updateReservationTime($update, $id_user_park)
    // {
    //     $reservation_buffer = ReservationBuffer::where('id_reservation', $id_user_park)->firstOrFail();

    //     $user_park = DB::table('user_parks')->update(
    //         array(
    //             'id_slot' => $reservation_buffer->id_slot,
    //             'arrive_time' => date('Y-m-d').' '.$update['arrive_time'],
    //             'leaving_time' => date('Y-m-d').' '.$update['leaving_time'],
    //             'price' => $update['price'],
    //         )
    //     );

    //     return $user_park;
    // }
}
