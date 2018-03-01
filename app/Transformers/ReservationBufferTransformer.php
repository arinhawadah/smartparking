<?php

namespace App\Transformers;

use App\ReservationBuffer;
use League\Fractal\TransformerAbstract;

class ReservationBufferTransformer extends TransformerAbstract
{
    public function transform(ReservationBuffer $reservation_buffer)
    {
        return[
            'id_reservation' => $reservation_buffer->id,
            'id_slot' => $reservation_buffer->id_slot,
            'validity_limit'=>$reservation_buffer->validity_limit,
            'reserved'=> $reservation_buffer->validity_limit->diffForHumans(),
        ];
    } 
    
}