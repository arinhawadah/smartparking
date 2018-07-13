<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HistoryTransaction;
use App\Transformers\HistoryTransformer;

class HistoryController extends Controller
{
     // get history each user
     public function historybyId(HistoryTransaction $history_of_transaction, $id_user)
     {
        $history_of_transaction = HistoryTransaction::where('id_user',$id_user)->orderBy('created_at','desc')->get();
        
        return fractal()
        ->collection($history_of_transaction)
        ->transformWith(new HistoryTransformer)
        ->toArray();
     }
}
