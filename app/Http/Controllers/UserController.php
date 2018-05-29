<?php

namespace App\Http\Controllers;

use App\User;
use App\UserPark;
use App\ReservationBuffer;
use Illuminate\Http\Request;
use App\Transformers\UserCredentialTransformer;
use Auth;
use JWTAuth;
use JWTAuthException;
use DB;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('jwt.auth', ['only' => ['showUserbyId']]);
    // }

    public function users(Request $request, User $user)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        // $users = $user->all();
        $users = User::paginate(10);
        
        if ($request->wantsJson())
        {
        return fractal()
        ->collection($users)
        ->transformWith(new UserCredentialTransformer)
        ->toArray();
        }

        return view('users-mgmt/index', ['users' => $users]);
    }

    public function profile(User $user)
    {
        $user = $user->find(Auth::user()->id_user);
        // $user = $user->find(JWTAuth::parseToken()->authenticate()->id_user);

        // return fractal()
        // ->item($user)
        // ->transformWith(new UserCredentialTransformer)
        // ->toArray();

        return response()->json([$user->name, $user->email]);
    }

    public function updateProfile(Request $request, User $user, $id_user)
    {
        $user = User::findOrFail($id_user);
        $constraints = [
            // 'name' => 'required',
            // 'email' => 'required|unique:user_credentials'.$user->id.',id_user',
            // 'car_type' => 'required',
            // 'license_plate_number' => 'required',
            'password' => 'required|min:6|confirmed',
            ];

        $input = [
            // 'name' => $request['name'],
            // 'email' => $request['email'],
            // 'car_type' => $request['car_type'],
            // 'license_plate_number' => $request['license_plate_number'],
            'password' =>  bcrypt($request['password']),
        ];
        
        $this->validate($request, $constraints);

        User::where('id_user', $id_user)->update($input);
        $edituser = User::findOrFail($id_user);

        if ($request->wantsJson())
        {
        return fractal()
        ->item($edituser)
        ->transformWith(new UserCredentialTransformer)
        ->toArray();
        }

        return redirect()->intended('admin/showuser');
    }

    public function deleteUser(Request $request, User $user, $id_user)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        DB::table('role_user')->where('user_id_user', $id_user)->delete();
        UserPark::where('id_user', $id_user)->delete();
        User::where('id_user', $id_user)->delete();

        return response()->json('Delete User Success');
    }

    //search user by id
    public function showUserbyId(Request $request, User $user, $id_user)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $user = User::findOrFail($id_user);

        if ($request->wantsJson())
        {
        return fractal()
        ->item($user)
        ->transformWith(new UserCredentialTransformer)
        ->toArray();
        }
        
        return view('users-mgmt/edit', ['user' => $user]);
    }

    //search user by email
    public function showUserbyEmail(Request $request, User $user)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $constraints = [
            'email' => $request['email'],
            ];

        // $user = $user
        // ->where('email', $email)
        // ->first();

       $users = $this->doSearchingQuery($constraints);

        // return fractal()
        // ->item($user)
        // ->transformWith(new UserCredentialTransformer)
        // ->toArray();

        return view('users-mgmt/index', ['users' => $users, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = User::query();
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        return $query->paginate(10);
    }

     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users-mgmt/create');
    }
}
