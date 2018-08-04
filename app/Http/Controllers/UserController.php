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

    public function index(Request $request, User $user)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $users = User::with('roles')->select('id_user','email','name','name','license_plate_number')->paginate(10);
        
        if ($request->wantsJson())
        {
        return response()->json($users);
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

    public function update(Request $request, User $user, $id_user)
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
        return response()->json("Success");
        }

        return redirect()->intended('/user-admin');
    }

    public function destroy(Request $request, User $user, $id_user)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        User::findOrFail($id_user);
        DB::table('role_user')->where('user_id_user', $id_user)->delete();
        UserPark::where('id_user', $id_user)->delete();
        User::where('id_user', $id_user)->delete();

        if ($request->wantsJson())
        {
        return response()->json("Delete Success");
        }

        return redirect()->intended('/user-admin');
    }

    //search user by id
    public function edit(Request $request, User $user, $id_user)
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

        if ($request->wantsJson())
        {
        return response()->json($users);
        }

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

    public function create()
    {
        return view('users-mgmt/create');
    }

    public function createUser()
    {
        return view('users-mgmt/createuser');
    }
}
