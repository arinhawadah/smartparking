<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\UserRegistration;
use Auth;
use DB;

class ActivationController extends Controller
{
    public function activate(Request $request)
    {
       
        $user_registration = UserRegistration::where('email', $request->email)->where('activation_token', $request->token)->firstOrFail();

        $user = User::create(
            [
                'unique_id' => $user_registration->unique_id, 
                'name' => $user_registration->name,
                'email' => $user_registration->email,
                'car_type' => $user_registration->car_type,
                'license_plate_number' => $user_registration->license_plate_number,
                'password' => $user_registration->password,
                // 'activation_token'=> str_random(25),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        UserRegistration::where('email', $request->email)->delete();

        $user
        ->roles()
        ->attach(Role::where('role_name', 'User')
        ->first());

        // Auth::loginUsingId($user->id);

        // return redirect()->route('home')->withSuccess('Aktif, skrg kamu signed in');

        // $img = file_get_contents(public_path('Faith.png'));
        // return response($img)->header('Content-type','image/jpg');
        return "Thank You";
    }

    
}
