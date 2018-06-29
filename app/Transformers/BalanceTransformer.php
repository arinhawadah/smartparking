<?php

namespace App\Transformers;

use App\UserBalance;
use League\Fractal\TransformerAbstract;

class BalanceTransformer extends TransformerAbstract
{
    public function transform(UserBalance $user_balance)
    {
        return[
            'balance' => (float) $user_balance->balance,
        ];
    } 
    
}