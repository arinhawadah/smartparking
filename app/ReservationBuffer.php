<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReservationBuffer extends Model
{
    public $timestamps = false;

    protected $dates = [
        'validity_limit'
    ];
    
    protected $fillable =[
        'id_user', 'id_slot', 'validity_limit',
    ];

    protected $primaryKey = 'id_reservation';
}
