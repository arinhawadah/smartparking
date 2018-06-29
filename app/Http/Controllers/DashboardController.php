<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserPark;
use DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    // get status by time arrive
    public function allSlotByTime(Request $request, UserPark $user_park)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $visitor_month = $user_park->select(DB::raw("COUNT(id_user_park) as count"), DB::raw('month(arrive_time) as month'))
        ->groupBy('month')
        ->get()->toArray();
        // dd($visitor_month);
        
        $visitor_month = array_column($visitor_month, 'count');

        $month = $user_park->select(DB::raw('date_format(arrive_time,"%b") as b'), DB::raw('month(arrive_time) as month'))
        ->groupBy('b', 'month')
        ->orderBy('month')
        ->get()->toArray();
        // dd($month);
        
        $month = array_column($month, 'b');

        $visitor_day = $user_park->select(DB::raw("COUNT(id_user_park) as count"), DB::raw("weekday(arrive_time) as weekday"))
        ->groupBy('weekday')
        ->orderBy('weekday')
        ->get()->toArray();

        // $visitor_day = $user_park->select('id_user_park')
        // ->where(DB::raw('weekday(arrive_time)'), '2')
        // ->get()->toArray();
        // dd($visitor_day);
        
        $visitor_day = array_column($visitor_day, 'count');
        // $day = array_column($visitor_day, 'day');

        $day = $user_park->select(DB::raw('date_format(arrive_time,"%a") as day'), DB::raw("weekday(arrive_time) as weekday"))
        ->groupBy('weekday','day')
        ->orderBy('weekday')
        ->get()->toArray();
        // dd($day);
        
        $day = array_column($day, 'day');

        $visitor_time = $user_park->select(DB::raw('COUNT(id_user_park) as count'))
        ->groupBy(DB::raw('hour(arrive_time)'))
        ->get()->toArray();
        
        $visitor_time = array_column($visitor_time, 'count');
        
        $time = $user_park->select(DB::raw('date_format(arrive_time,"%H:%00") as time'))
        ->groupBy('time')
        ->get()->toArray();

        // $time = $user_park->select(DB::raw('hour(arrive_time) as time'))
        // ->groupBy('time')
        // ->get()->toArray();
        
        $time = array_column($time, 'time');

        // return fractal()
        // ->collection($user_park)
        // ->transformWith(new UserParkTimeTransformer)
        // ->toArray();

        // return response()->json($visitor_times);
        return view('dashboard')
            ->with('visitor_day',json_encode($visitor_day,JSON_NUMERIC_CHECK))
            ->with('visitor_month',json_encode($visitor_month,JSON_NUMERIC_CHECK))
            ->with('visitor_time',json_encode($visitor_time,JSON_NUMERIC_CHECK))
            ->with('time',json_encode($time,JSON_NUMERIC_CHECK))
            ->with('month',json_encode($month,JSON_NUMERIC_CHECK))
            ->with('day',json_encode($day,JSON_NUMERIC_CHECK));
    }
}
