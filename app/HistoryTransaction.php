<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryTransaction extends Model
{
    protected $primaryKey = 'id_history';

    protected $fillable =[
        'id_slot', 'id_user','status_transaction', 'id_user_park',
    ];
}
