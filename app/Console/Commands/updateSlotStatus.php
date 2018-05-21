<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class updateSlotStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateStatus:updatestatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Slot Status Updated';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // DB::table('car_park_slots')
        // ->leftJoin('user_parks','car_park_slots.id_slot','=','user_parks.id_slot')
        // ->whereDate('arrive_time', date('Y-m-d'))
        // ->whereDate('leaving_time', date('Y-m-d'))       
        // ->where('arrive_time','=',date('Y-m-d H:i:00')->setTimezone("Asia/Jakarta"))
        // ->update(['status' => 'OCCUPIED']);

        DB::table('car_park_slots')
        ->leftJoin('user_parks','car_park_slots.id_slot','=','user_parks.id_slot')
        ->whereDate('arrive_time', date('Y-m-d'))
        ->whereDate('leaving_time', date('Y-m-d'))       
        ->where('arrive_time','=',now()->setTimezone("Asia/Jakarta")->format('Y-m-d H:i:00'))
        ->update(['status' => 'OCCUPIED']);

        DB::table('park_sensors')
        ->leftJoin('car_park_slots','park_sensors.id_sensor','=','car_park_slots.id_sensor')
        ->where('car_park_slots.status','OCCUPIED')
        ->update(
            ['park_sensors.status' => 2, 
            'time' => now(),
            ]
        );

        DB::table('park_sensors')
        ->leftJoin('car_park_slots','park_sensors.id_sensor','=','car_park_slots.id_sensor')
        ->where('car_park_slots.status','AVAILABLE')
        ->update(
            ['park_sensors.status' => 1, 
            'time' => now(),
            ]
        );

        DB::table('park_sensors')
        ->leftJoin('car_park_slots','park_sensors.id_sensor','=','car_park_slots.id_sensor')
        ->where('car_park_slots.status','PARKED')
        ->update(
            ['park_sensors.status' => 3, 
            'time' => now(),
            ]
        );

        // $slot = DB::table('car_park_slots')
        // ->where('status', 'OCCUPIED')->firstOrFail();

        // DB::table('car_park_slot_dumps')
        // ->create(
        //     [
        //     'id_slot' => $slot['id_slot'],
        //     'id_sensor' => $slot['id_sensor'],
        //     'status'  => 'DIBOOKING',
        //     'slot_name' => $slot['slot_name'],
        //     ]
        // );

    	$this->info('Slot Status Update Successfully!');
    }
}
