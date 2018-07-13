<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryTransaction extends Model
{
    protected $primaryKey = 'id_history';

    protected $table = 'history_of_transactions';

    protected $fillable =[
        'id_slot', 'id_user', 'id_user_park',
    ];
}
