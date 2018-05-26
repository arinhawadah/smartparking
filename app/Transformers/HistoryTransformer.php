<?php

namespace App\Transformers;

use App\HistoryTransaction;
use App\CarParkSlot;
use League\Fractal\TransformerAbstract;

class HistoryTransformer extends TransformerAbstract
{
    public function transform(HistoryTransaction $history_transaction)
    {
        $car_park_slot = CarParkSlot::where('id_slot', $history_transaction->id_slot)->select('slot_name')->firstOrFail();
        return[
            'slot name' =>$car_park_slot->slot_name,
            'price' => $history_transaction->price,
            'time' => $history_transaction->created_at->setTimezone("Asia/Jakarta")->format('l, d/m/Y H:i'),
        ];
    }
}