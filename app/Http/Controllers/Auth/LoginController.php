<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\User;
use Auth;
use JWTAuth;
use JWTAuthException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin/allslot';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

     /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => [
                'required','string',
                Rule::exists('user_credentials')
            ],
        ], ['Your email have not registerd']);

        if (!Auth::attempt(['email'=>$request->email,'password'=>$request->password],true)){
            return response()->json(['error'=>'Your credential is wrong'],401);
        }
    }

    /**
     * validasi error
     */
    public function validationError()
    {
        return [
            $this->username() . '.exists' => 'Silahkan Verifikasi Email Terlebih Dahulu'
        ] ;
    }
}
