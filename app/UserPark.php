<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPark extends Model
{
    protected $primaryKey = 'id_user_park';
    
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_user','id_slot', 'unique_id', 'arrive_time', 'leaving_time','price'
    ];
}
