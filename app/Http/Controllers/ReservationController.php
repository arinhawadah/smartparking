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

        $users = $user_park
        ->leftJoin('user_credentials','user_parks.id_user','=','user_credentials.id_user')
        ->leftJoin('car_park_slots','user_parks.id_slot','=','car_park_slots.id_slot')
        // ->whereMonth('arrive_time','=',date('m'))
        ->select('car_park_slots.slot_name','user_parks.arrive_time','user_parks.leaving_time','user_parks.price',
        'user_parks.id_user_park','user_credentials.name')
        ->orderBy('id_user_park', 'desc')
        ->paginate(10);

        if ($request->wantsJson())
        {
            return response()->json($users);
        }

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
            'id_slot' => $request->id_slot,
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
            'id_slot' => $request->id_slot,
            'unique_id' => $user->unique_id, 
            'arrive_time' => $request->arrive_time,
            'leaving_time' => $request->leaving_time,
            'price' => $request->price,
        ]);

        $this->historyTransactionfromAdmin($user_park);

        if ($request->wantsJson())
        {
            return response()->json("Success");
        }
        return redirect()->intended('/reservation-admin');
    }

    // update reservation
    public function updateReservation(Request $request, UserPark $user_park, $id_user_park)
    {
        $user_park = UserPark::findOrFail($id_user_park);

        $constraints = [
            'id_slot' => 'required',
            'price'   => 'required',
            ];

        $this->validate($request, $constraints);

        // $this->updateReservationTime($update, $id_reservation); // update reservation time 
        $user_park->where('id_user_park', $id_user_park)
            ->update(
                [
                    'arrive_time' => now()->format('Y-m-d H:i:00'),
                    'id_slot' => $request->id_slot,
                    'price'   => $user_park['price'] + $request->price,
                ]
            );
        
        $old_balance = UserBalance::where('id_user', $user_park['id_user'])->pluck('balance')->first();

        $new_balance = $old_balance - $request->price;

        if($new_balance < 0)
        {
            return response()->json(['error' => 'Insufficient balance'], 402);
        }         

        UserBalance::where('id_user', $user_park['id_user'])->update(['balance' => $new_balance]);

        $editreservation = UserPark::findOrFail($id_user_park);

        $this->updateHistoryTransaction($editreservation);
        
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
                    'id_slot' => $id_slot,
                    'arrive_time' => $update['arrive_time'],
                    'leaving_time' => $update['leaving_time'],
                    'price' => $update['price'],
                ]
            );
    
            $editreservation = UserPark::findOrFail($id_user_park);
            
            $this->updateHistoryTransactionfromAdmin($editreservation);

            if ($request->wantsJson())
            {
                return response()->json("Success");
            }

            return redirect()->intended('reservation-admin');
    }

    // update status expired reservation
    public function updateStatus($id_user_park)
    {
        $user_park = UserPark::findOrFail($id_user_park);
        $car_park_slot = CarParkSlot::where('id_slot',$user_park['id_slot'])->pluck('id_sensor')->first();

        CarParkSlot::where('id_slot',$user_park['id_slot'])->update(
            ['status' => 'AVAILABLE']
        );

        ParkSensor::where('id_sensor',$car_park_slot)->update(
            ['status' => 0]
        );

        return response()->json('Success');
    }

    // delete reservation
    public function destroy(Request $request, $id_user_park)
    {
        // $request->user()->authorizeRoles(['Super Admin', 'Admin', 'User']);

        $user_park = UserPark::where('id_user_park', $id_user_park)->select('price','id_user')->firstOrFail();
        $old_balance = UserBalance::where('id_user', $user_park['id_user'])->pluck('balance')->first();

        $new_balance = [
            'balance' => $old_balance + $user_park['price'],
        ];

        UserBalance::where('id_user', $user_park['id_user'])->update($new_balance);
        HistoryTransaction::where('id_user_park', $id_user_park)->delete();
        UserPark::where('id_user_park', $id_user_park)->delete();

        if ($request->wantsJson()){
        return response()->json('Delete Success');
        }

        return redirect()->intended('reservation-admin');
    }

    //history transaction user
    private function historyTransaction($user_park)
    {
        $history_of_transaction = HistoryTransaction::insert(
            [
                'id_slot' => $user_park['id_slot'],
                'id_user' => JWTAuth::parseToken()->authenticate()->id_user,
                'id_user_park'  => $user_park['id_user_park'],
                'price' => $user_park['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        return $history_of_transaction;
    }

    //history transaction update user
    private function updateHistoryTransaction($editreservation)
    {
        $history_of_transaction = HistoryTransaction::where('id_user_park', $editreservation['id_user_park'])
        ->update(
            [
                'id_slot' => $editreservation['id_slot'],
                'price' => $editreservation['price'],
                'updated_at' => now(),
            ]
        );
        return $history_of_transaction;
    }

    //history transaction admin
    private function historyTransactionfromAdmin($user_park)
    {
        $history_of_transaction = HistoryTransaction::insert(
            [
                'id_slot' => $user_park['id_slot'],
                'id_user' => $user_park['id_user'],
                'id_user_park'  => $user_park['id_user_park'],
                'price' => $user_park['price'],
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        return $history_of_transaction;
    }

    //history transaction update admin
    private function updateHistoryTransactionfromAdmin($editreservation)
    {
        $history_of_transaction = HistoryTransaction::where('id_user_park', $editreservation['id_user_park'])
        ->update(
            [
                'id_slot' => $editreservation['id_slot'],
                'price' => $editreservation['price'],
                'updated_at' => now(),
            ]
        );
        return $history_of_transaction;
    }

    //search reservation by id_user_park
    public function edit(Request $request, $id_user_park)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $user = UserPark::findOrFail($id_user_park);
        $slot = CarParkSlot::all();

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

       if ($request->wantsJson())
       {
           return response()->json($users);
       }
        return view('reservation-mgmt/index', ['users' => $users, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = UserPark::leftJoin('user_credentials','user_parks.id_user','=','user_credentials.id_user')
        ->leftJoin('car_park_slots','user_parks.id_slot','=','car_park_slots.id_slot')
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
}
