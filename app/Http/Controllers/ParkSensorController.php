<?php

namespace App\Http\Controllers;
use App\ParkSensor;
use App\ParkSensorResp;
use App\CarParkSlot;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Transformers\ParkSensorTransformer;

class ParkSensorController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['deleteParkSensor']]);
    }

    // delete park_sensor
    public function deleteParkSensor(Request $request, $id_sensor)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        // CarParkSlotDump::where('id_sensor', $id_sensor)->delete();
        ParkSensor::where('id_sensor', $id_sensor)->delete();
        ParkSensorResp::where('id_sensor', $id_sensor)->delete();

        return response()->json('Delete Success');
    }

    //get status from database
    public function getSensorStatus(ParkSensor $park_sensor, $id_sensor)
    {
        $sensor_status = $park_sensor->where('id_sensor', $id_sensor)
        ->pluck('status')
        ->first();

        return $sensor_status;
    }
}
