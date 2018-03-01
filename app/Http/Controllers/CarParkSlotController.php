<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CarParkSlot;
use App\Transformers\CarParkSlotTransformer;

class CarParkSlotController extends Controller
{
    public function status(CarParkSlot $car_park_slot)
    {
        $car_park_slot = $car_park_slot->all();

        return fractal()
        ->collection($car_park_slot)
        ->transformWith(new CarParkSlotTransformer)
        ->toArray();

        return response()->json($response, 201);
    }
}
