<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\Auth\UserActivationEmail;
use Illuminate\Validation\Rule;
use App\Http\Requests;
use App\UserRegistration;
use App\User;
use App\Role;
use App\Transformers\UserTransformer;
use App\Transformers\UserCredentialTransformer;
use Mail;
use Auth;

class AuthController extends Controller
{
    public function register(Request $request, UserRegistration $user_registration)
    {
        $this->validate($request, [
            'name' => 'required',
            'email'=> 'required|email|unique:user_registrations',
            'password'=>'required|min:6',
            'car_type'=>'required',
            'license_plate_number' => 'required|max:10',
        ]);

        $user_registration = $user_registration->create([
            'unique_id' => str_random(25),
            'name' => $request->name,
            'email' => $request->email,
            'password'=>bcrypt($request->password),
            'activation_token' => str_random(225),
            'car_type' => $request->car_type,
            'license_plate_number' => $request->license_plate_number,
        ]);

        $user_registration->roles()->attach(Role::where('role_name', 'User')->first());
        
        //sending email
        event(new UserActivationEmail($user_registration));
        // $this->guard()->logout();
   
        // Mail::send('emails.auth.activation', ['user' => $user], function($message) {
        // $message->to($request->email)
        //         ->subject('SPark');
        // $message->from('spark@mail.com','SparkEmail');
        // });

        return fractal()
        ->item($user_registration)
        ->transformWith(new UserTransformer)
        ->addMeta([
            'activation token' => $user_registration->activation_token,
        ])
        ->toArray();

        return response()->json($response, 201);
    }

    public function login(Request $request, User $user)
    {
        $this->validate($request, [
            'email' => [
                'required','string',
                Rule::exists('user_credentials')
            ],
        ], ['Please verifiy your email']);
        
        if (!Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            return response()->json(['error'=>'Your credential is wrong',401]);
        }

        // if (Auth::attempt(array('email' => $request->email, 'password' => $request->password), true))
        // {
        //     // The user is being remembered...
        // }

        $user = $user->find(Auth::user()->id_user);

        return fractal()
        ->item($user)
        ->transformWith(new UserCredentialTransformer)
        ->addMeta([
            'activation token' => $user->activation_token,
        ])
        ->toArray();
    }
}
