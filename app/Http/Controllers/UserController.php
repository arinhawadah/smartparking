<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Transformers\UserCredentialTransformer;
use Auth;

class UserController extends Controller
{
    public function users(User $user)
    {
        $users = $user->all();

        return fractal()
        ->collection($users)
        ->transformWith(new UserCredentialTransformer)
        ->toArray();
    }

    public function profile(User $user)
    {
        $user = $user->find(Auth::user()->id_user);

        return fractal()
        ->item($user)
        ->transformWith(new UserCredentialTransformer)
        ->toArray();
    }

    public function updateProfile(Request $request, User $user, $id_user)
    {
        $user = User::findOrFail($id_user);
        $constraints = [
            'name' => 'required',
            'email' => 'required|unique:user_credentials'.$user->id.',id_user',
            'car_type' => 'required',
            'license_plate_number' => 'required',
            ];

        $input = [
            'name' => $request['name'],
            'email' => $request['email'],
            'car_type' => $request['car_type'],
            'license_plate_number' => $request['license_plate_number'],
        ];
        
        $this->validate($request, $constraints);

        User::where('id_user', $id_user)->update($input);
        $edituser = User::findOrFail($id_user);

        return fractal()
        ->item($edituser)
        ->transformWith(new UserCredentialTransformer)
        ->toArray();
    }
}
