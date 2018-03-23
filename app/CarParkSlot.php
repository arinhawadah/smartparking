<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarParkSlot extends Model
{
    protected $primaryKey = 'id_slot';

    public $timestamps = false;

    protected $fillable =[
        'id_slot', 'status', 'slot_name', 'id_sensor',
    ];
}
