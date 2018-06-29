<?php

namespace App\Http\Controllers;
use App\ParkSensor;
use App\ParkSensorResponse;
use App\CarParkSlot;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\ParkSensorTransformer;

class ParkSensorController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('jwt.auth', ['only' => ['deleteParkSensor']]);
    // }

    public function create()
    {
        return view('sensor-mgmt/create');
    }

    // add new sensor or update sensor status
    public function store(Request $request, ParkSensor $park_sensor)
    {
        $this->validate($request, [
            'id_sensor' => 'required|unique:park_sensors',
            'status'=> 'required',
        ]);

        $park_sensor = $park_sensor->create([
            'id_sensor' => $request->id_sensor,
            'status' => $request->status,
            'time' => now()]
        );
        
        return redirect()->intended('/sensor-admin');
    }

    // get all sensor
    public function index(Request $request, ParkSensor $park_sensor)
    {
        $park_sensor = $park_sensor->paginate($park_sensor->count());

        return view('sensor-mgmt/index', ['slot' => $park_sensor]);
    }

    // delete park_sensor
    public function destroy(Request $request, $id_sensor)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        // CarParkSlotDump::where('id_sensor', $id_sensor)->delete();
        ParkSensor::where('id_sensor', $id_sensor)->delete();
        ParkSensorResponse::where('id_sensor', $id_sensor)->delete();

        return redirect()->intended('/sensor-admin');
    }

    //get status from database
    public function getSensorStatus(ParkSensor $park_sensor, $id_sensor)
    {
        $sensor_status = $park_sensor->where('id_sensor', $id_sensor)
        ->pluck('status')
        ->first();

        return $sensor_status;
    }

    //search sensor by id_sensor
    public function edit(Request $request, $entry)
    {
        $sensor = ParkSensor::findOrFail($entry);
        
        return view('sensor-mgmt/edit', ['sensor' => $sensor]);
    }

    // update sensor status
    public function update(Request $request, $entry)
    {
        $this->validate($request, [
            'status'=> 'required',
        ]);

        $input = ['status' => $request->status];

        $id_sensor = ParkSensor::where('entry', $entry)->pluck('id_sensor');
        $this->updateStatusSlot($input, $id_sensor);
        $park_sensor = ParkSensor::where('entry', $entry)->update($input);
        
        return redirect()->intended('/sensor-admin');
    }

    // update status car_park_slot
    private function updateStatusSlot($input, $id_sensor)
    {
        if($input == 0 ){
            $car_park_slot = CarParkSlot::where('id_sensor',$id_sensor)->update(
                ['status' => 'AVAILABLE']
              );
        }  
        elseif($input == 1){
            $car_park_slot = CarParkSlot::where('id_sensor',$id_sensor)->update(
                ['status' => 'PARKED']
              );
        }
        elseif($input == 2){
            $car_park_slot = CarParkSlot::where('id_sensor',$id_sensor)->update(
                ['status' => 'OCCUPIED']
              );
        }
        return;
    }
}
