<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\CarParkSlot;
// use App\CarParkSlotDump;
use App\UserPark;
use App\ParkSensor;
use App\Transformers\CarParkSlotTransformer;
use App\Transformers\UserParkTimeTransformer;
use App\Transformers\AvailableTimeTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CarParkSlotController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('jwt.auth', ['only' => ['createParkSlot', 'deleteParkSlot']]);
    // }

    // // get all park slot
    // public function cobacoba(CarParkSlot $car_park_slot)
    // {
    //     $car_park_slot = $car_park_slot
    //     ->leftJoin('user_parks','car_park_slots.id_slot','=','user_parks.id_slot')
    //     ->whereDate('arrive_time', date('Y-m-d'))
    //     ->whereDate('leaving_time', date('Y-m-d'))       
    //     ->where('arrive_time','=',now()->setTimezone("Asia/Jakarta")->format('Y-m-d H:i:00'))
    //     ->get();

    //     return fractal()
    //     ->collection($car_park_slot)
    //     ->transformWith(new CarParkSlotTransformer)
    //     ->toArray();

    //     return response()->json($response, 201);
    // }

    // get all park slot
    public function index(Request $request, CarParkSlot $car_park_slot)
    {
        // $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $car_park_slot = $car_park_slot->paginate($car_park_slot->count());

        if ($request->wantsJson())
        {
        return fractal()
        ->collection($car_park_slot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();
        }

        return view('slot-mgmt/index', ['slot' => $car_park_slot]);
    }

    // get all park slot
    public function statusById(CarParkSlot $car_park_slot, UserPark $user_park, $id_user_park)
    {
        $user_park = $user_park
        ->where('id_user_park','=', $id_user_park)
        ->select('id_slot', 'arrive_time', 'leaving_time')
        ->first();

        $car_park_slot = $car_park_slot
        ->where('id_slot','=', $user_park['id_slot'])
        ->first();

        if($car_park_slot['status'] == 'PARKED'||$car_park_slot['status'] == 'OCCUPIED')
        {
            $sub_time_1 = strtotime($user_park['arrive_time']) - 60; // "- 60" means, arrive_time - 1 minutes
            $arrive_time1 = date('H:i', $sub_time_1);
            $sub_time_2 = strtotime($user_park['leaving_time']) - 60;
            $leaving_time1 = date('H:i', $sub_time_2);
    
            $sub_time_3 = strtotime($user_park['arrive_time']) + 60; // "+ 60" means, arrive_time + 1 minutes
            $arrive_time2 = date('H:i', $sub_time_3);
            $sub_time_4 = strtotime($user_park['leaving_time']) + 60;
            $leaving_time2 = date('H:i', $sub_time_4);
    
            $day_check = $user_park->whereDate('arrive_time', date('Y-m-d'))->first();
            // dd($day_check);
    
            if ($day_check != NULL)
            {
                try { //cobain pake foreach deh rin misal cek slot 1 -> ada jam itu atau nggak, kalo ada lanjut ke slot 2,3,4,5,dst
                    $from = min(now(), $user_park['leaving_time']);
                    $till = max(now(), $user_park['leaving_time']);

                    $user_park = UserPark::where('arrive_time', '<=', date('Y-m-d').' '.$from)
                    ->where('leaving_time', '>=', date('Y-m-d').' '.$till)
                    ->orWhereBetween('arrive_time', [date('Y-m-d').' '.$arrive_time1, date('Y-m-d').' '.$leaving_time1])
                    ->orWhereBetween('leaving_time',[date('Y-m-d').' '.$arrive_time2, date('Y-m-d').' '.$leaving_time2])
                    ->select('user_parks.id_slot')
                    ->orderBy('user_parks.id_slot','asc')
                    ->groupBy('id_slot')
                    // ->firstOrFail();
                    ->get()->toArray();

                    $car_park_slot = $car_park_slot
                    ->where('status','<>' ,'PARKED')
                    ->whereNotIn('id_slot', $user_park)
                    ->select('car_park_slots.*')
                    ->orderBy('car_park_slots.id_slot','asc')
                    ->firstOrFail();
                    
                  } catch (ModelNotFoundException $ex) {
                    return response()->json("Full Booked", 404);
                  }       
            }
            else
            {
                $car_park_slot = $car_park_slot
                ->where('status', 'AVAILABLE')
                ->select('car_park_slots.*')
                ->orderBy('car_park_slots.id_slot','asc')
                // ->take(0)
                ->firstOrFail();
            }
        }

        return fractal()
        ->item($car_park_slot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();
    }

    // get first available slot
    public function statusAvailableSlot(CarParkSlot $car_park_slot, $arrive_time, $leaving_time, UserPark $user_park)
    {
        $sub_time_1 = strtotime($arrive_time) - 60; // "- 60" means, arrive_time - 1 minutes
        $arrive_time1 = date('H:i', $sub_time_1);
        $sub_time_2 = strtotime($leaving_time) - 60;
        $leaving_time1 = date('H:i', $sub_time_2);

        $sub_time_3 = strtotime($arrive_time) + 60; // "+ 60" means, arrive_time + 1 minutes
        $arrive_time2 = date('H:i', $sub_time_3);
        $sub_time_4 = strtotime($leaving_time) + 60;
        $leaving_time2 = date('H:i', $sub_time_4);

        $day_check = $user_park->whereDate('arrive_time', date('Y-m-d'))->first();
        // dd($day_check);

        if ($day_check != NULL)
        {
            try {
                    $from = min($arrive_time, $leaving_time);
                    $till = max($arrive_time, $leaving_time);

                    $user_park = UserPark::where('arrive_time', '<=', date('Y-m-d').' '.$from)
                    ->where('leaving_time', '>=', date('Y-m-d').' '.$till)
                    ->orWhereBetween('arrive_time', [date('Y-m-d').' '.$arrive_time1, date('Y-m-d').' '.$leaving_time1])
                    ->orWhereBetween('leaving_time',[date('Y-m-d').' '.$arrive_time2, date('Y-m-d').' '.$leaving_time2])
                    ->select('user_parks.id_slot')
                    ->orderBy('user_parks.id_slot','asc')
                    ->groupBy('id_slot')
                    // ->firstOrFail();
                    ->get()->toArray();

                   $car_park_slot = $car_park_slot
                   ->whereNotIn('id_slot', $user_park)
                   ->select('car_park_slots.id_slot','car_park_slots.slot_name')
                   ->orderBy('car_park_slots.id_slot','asc')
                   ->firstOrFail();

              } catch (ModelNotFoundException $ex) {
                return response()->json("Full Booked", 404);
              }       
        }
        else
        {
            $car_park_slot = $car_park_slot
            ->where('status', 'AVAILABLE')
            ->select('car_park_slots.id_slot','car_park_slots.slot_name')
            ->orderBy('car_park_slots.id_slot','asc')
            // ->take(0)
            ->firstOrFail();
        }

        return fractal()
                ->item($car_park_slot)
                ->transformWith(new AvailableTimeTransformer)
                ->toArray();
        
                return response()->json($response, 201);
    }

    // // get status by time arrive
    // public function slotByTime(UserPark $user_park, $time)
    // {
    //     // $user_park = $user_park
    //     // ->whereDate('arrive_time','=', $time)
    //     // ->orWhereTime('leaving_time','=', $time)
    //     // ->get();

    //     $user_park = $user_park
    //     ->whereDay('arrive_time', $time)
    //     ->whereDay('leaving_time', $time)
    //     ->count();

    //     return response()->json($user_park, 201);
    // }

    //search slot by id_user_park
    public function edit(Request $request, $id_slot)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $slot = CarParkSlot::findOrFail($id_slot);
        $sensor = ParkSensor::all();
        
        return view('slot-mgmt/edit', ['slot' => $slot, 'sensor' => $sensor]);
    }

    public function create()
    {
        $slot = CarParkSlot::all();
        $sensor = ParkSensor::all();

        return view('slot-mgmt/create', ['slot' => $slot, 'sensor' => $sensor]);
    }

    // create new slot
    public function store(Request $request, CarParkSlot $car_park_slot)
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

        // $slot_name = $request->slot_name;
        $input = $request->all();

        $this->updateSensor($input);
        
        if ($request->wantsJson())
        {
            return response()->json("Success");
        }

        return redirect()->intended('/slot-admin');
    }

    // update status park_sensor
    private function updateSensor($input)
    {
        if($input['status'] == 'AVAILABLE'){
            $park_sensor = ParkSensor::where('id_sensor',$input['id_sensor'])->update(
                ['status' => 0]
            );
        }
        elseif($input['status'] == 'PARKED'){
            $park_sensor = ParkSensor::where('id_sensor',$input['id_sensor'])->update(
                ['status' => 1]
            );
        }
        elseif($input['status'] == 'OCCUPIED'){
            $park_sensor = ParkSensor::where('id_sensor',$input['id_sensor'])->update(
                ['status' => 2]
            );
        }

        return;
    }

    // update status car_park_slot
    public function update(Request $request, CarParkSlot $car_park_slot, $id_slot)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        // $car_park_slot = CarParkSlot::where($id_slot);
        
        $constraints = [
            'status' => 'required',
            'id_sensor' => 'required|exists:park_sensors,id_sensor',
            ];

        $input = [
            'status' => $request['status'],
            'id_sensor' => $request['id_sensor'],
        ];
        
        $this->validate($request, $constraints);

        CarParkSlot::where('id_slot', $id_slot)->update($input);
        $this->updateSensor($input);

        $editslot = CarParkSlot::findOrFail($id_slot);

        if ($request->wantsJson())
        {
            return response()->json("Success");
        }

        return redirect()->intended('/slot-admin');

    }

    // delete car_park_slot
    public function destroy(Request $request, $id_slot)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        // CarParkSlotDump::where('id_slot', $id_slot)->delete();
        CarParkSlot::findOrFail($id_slot);
        CarParkSlot::where('id_slot', $id_slot)->delete();

        if($request->wantsJson())
        {
        return response()->json('Delete Success');
        }

        return redirect()->intended('/slot-admin');
    }
    
}
