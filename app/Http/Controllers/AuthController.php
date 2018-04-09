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
use JWTAuth;
use JWTAuthException;

class AuthController extends Controller
{
    // register
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
            'activation_token' => str_random(25),
            'car_type' => $request->car_type,
            'license_plate_number' => $request->license_plate_number,
        ]);
       
        //sending email
        event(new UserActivationEmail($user_registration));
        // $this->guard()->logout();
   
        // Mail::send('emails.auth.activation', ['user' => $user], function($message) {
        // $message->to($request->email)
        //         ->subject('SPark');
        // $message->from('spark@mail.com','SparkEmail');
        // });

        // return fractal()
        // ->item($user_registration)
        // ->transformWith(new UserTransformer)
        // ->addMeta([
        //     'activation token' => $user_registration->activation_token,
        // ])
        // ->toArray();

        // return response()->json($response, 201);

        return response()->json('Register Success');
    }

    // resgister admin
    public function registerAdmin(Request $request, User $user)
    {
        $request->user()->authorizeRoles(['Super Admin']);

        $this->validate($request, [
            'name' => 'required',
            'email'=> 'required|email|unique:user_registrations',
            'password'=>'required|min:6',
            'car_type'=>'required',
            'license_plate_number' => 'required|max:10',
        ]);

        $user = $user->create([
            'unique_id' => str_random(25),
            'name' => $request->name,
            'email' => $request->email,
            'password'=>bcrypt($request->password),
            'activation_token' => str_random(25),
            'car_type' => $request->car_type,
            'license_plate_number' => $request->license_plate_number,
        ]);

        $user
        ->roles()
        ->attach(Role::where('role_name', 'Admin')
        ->first());
       
        // return fractal()
        // ->item($user)
        // ->transformWith(new UserCredentialTransformer)
        // ->addMeta([
        //     'activation token' => $user->activation_token,
        // ])
        // ->toArray();

        // return response()->json($response, 201);
        return response()->json('Register Success');
    }

    // login
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

        $token = null;
            try{
                if(!$token = JWTAuth::attempt(['email'=>$request->email,'password'=>$request->password])) {
                    return response()->json([
                        'msg' => 'Email or Password are incorrect',
                    ], 404);
                } 
            } catch (JWTAuthException $e) {
                return response()->json([
                    'msg' => 'failed_to_create_token',
                ], 404);
            }

        $user = $user->find(Auth::user()->id_user);

        return fractal()
        ->item($user)
        ->transformWith(new UserCredentialTransformer)
        ->addMeta([
            // 'token' => $user->activation_token,
            'token' => $token,
        ])
        ->toArray();
    }
}
