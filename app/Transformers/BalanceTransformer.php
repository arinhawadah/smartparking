<?php

namespace App\Transformers;

use App\UserBalance;
use League\Fractal\TransformerAbstract;

class BalanceTransformer extends TransformerAbstract
{
    public function transform(UserBalance $user_balance)
    {
        return[
            'id_user' => $user_balance->id_slot,
            'slot_name' => $car_park_slot->slot_name,
        ];
    } 
    
}