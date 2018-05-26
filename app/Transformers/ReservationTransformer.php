<?php

namespace App\Transformers;

// use App\ReservationBuffer;
use App\UserPark;
use League\Fractal\TransformerAbstract;

class ReservationTransformer extends TransformerAbstract
{
    public function transform(UserPark $user_park)
    {
        // $user_park = UserPark::where('id_reservation', $reservation_buffer->id_reservation)->firstOrFail();
        return[
            'id_user_park' => $user_park->id_user_park,
            'id_slot' => $user_park->id_slot_user_park,
            'arrive_time' => $user_park->arrive_time,
            'leaving_time' => $user_park->leaving_time,
            'price' => $user_park->price,
            // 'validity_limit'=>$reservation_buffer->validity_limit->setTimezone("Asia/Jakarta")->format('H:i:s'),
            // 'reserved'=> $reservation_buffer->validity_limit->diffForHumans(),
        ];
    } 
    
}