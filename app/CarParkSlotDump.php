<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarParkSlotDump extends Model
{
    protected $primaryKey = 'id_dump';

    protected $fillable =[
        'id_slot', 'status', 'slot_name', 'id_sensor'
    ];
}
