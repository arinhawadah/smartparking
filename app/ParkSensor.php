<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParkSensor extends Model
{   
    public $timestamps = false;

    protected $fillable =[
        'id_sensor', 'status', 'time',
    ];

    protected $primaryKey = 'entry';
}
