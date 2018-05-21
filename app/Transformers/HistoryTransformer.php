<?php

namespace App\Transformers;

use App\HistoryTransaction;
use League\Fractal\TransformerAbstract;

class HistoryTransformer extends TransformerAbstract
{
    public function transform(HistoryTransaction $history_transaction)
    {
        return[
            'id slot' =>$history_transaction->id_slot,
            'price' => $history_transaction->price,
            'status' => $history_transaction->status_transaction,
            'time' => $history_transaction->created_at->setTimezone("Asia/Jakarta")->format('l,Y-m-d H:i'),
        ];
    }
}