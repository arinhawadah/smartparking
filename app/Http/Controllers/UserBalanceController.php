<?php

namespace App\Http\Controllers;
use App\UserBalance;
use App\User;
use Illuminate\Database\QueryException;
use App\Transformers\BalanceTransformer;
use Illuminate\Http\Request;

class UserBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->user()->authorizeRoles(['Super Admin', 'Admin']);

        $user_balance = UserBalance::leftJoin('user_credentials', 
        'user_credentials.id_user','=','user_balances.id_user')
        ->select('email','user_balances.*')
        ->paginate(10);

        if ($request->wantsJson())
        {
            return response()->json($user_balance);
        }
        
        return view('balance-mgmt/index', ['balance' => $user_balance]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('balance-mgmt/create'); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $this->validate($request,[
                'email' => 'required|exists:user_credentials,email',
                'balance' => 'required',
            ]);

            $user = User::where('email','=', $request->email)->select('id_user')->first();

            $user_balance = UserBalance::create([
                'id_user' => $user->id_user,
                'balance' => $request->balance,
            ]);
        }
        catch(QueryException $e){
            $errorCode = $e->errorInfo[1];
            if($errorCode == '1062'){
                return redirect()->back();
            }
        }

        if ($request->wantsJson())
        {
            return response()->json("Success");
        }

        return redirect()->intended('balance-admin');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(UserBalance $user_balance, $id_user)
    {
        $user_balance = $user_balance->where('id_user', $id_user)->select('balance')->first();

        return fractal()
        ->item($user_balance)
        ->transformWith(new BalanceTransformer)
        ->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id_balance)
    {
        $user_balance = UserBalance::leftJoin('user_credentials', 
        'user_credentials.id_user','=','user_balances.id_user')
        ->where('id_balance', $id_balance)
        ->select('user_credentials.name','user_balances.*')
        ->first();

        return view('balance-mgmt/edit', ['balance' => $user_balance]); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_balance)
    {
        $constraints = [
            'balance' => 'required',
            ];

        $input = [
            'balance' =>  $request['balance'],
            ];
            
        $this->validate($request, $constraints);

        UserBalance::where('id_balance', $id_balance)->update($input);

        if ($request->wantsJson())
        {
            return response()->json("Success");
        }

        return redirect()->intended('/balance-admin');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id_balance)
    {
        ParkSensor::findOrFail($id_balance);
        UserBalance::where('id_balance', $id_balance)->delete();

        if ($request->wantsJson())
        {
            return response()->json("Delete Success");
        }

        return redirect()->intended('/balance-admin');
    }

    public function search(Request $request)
    {
        $constraints = [
            'email' => $request['email'],
            ];

       $balance = $this->doSearchingQuery($constraints);

       if ($request->wantsJson())
       {
           return response()->json($balance);
       }
        return view('balance-mgmt/index', ['balance' => $balance, 'searchingVals' => $constraints]);
    }

    private function doSearchingQuery($constraints) {
        $query = UserBalance::leftJoin('user_credentials', 
        'user_credentials.id_user','=','user_balances.id_user')
        ->select('user_credentials.email','user_balances.*');
        $fields = array_keys($constraints);
        $index = 0;
        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                $query = $query->where( $fields[$index], 'like', '%'.$constraint.'%');
            }

            $index++;
        }
        $querys = $query->count();
        return $query->paginate($querys);
    }

    public function penaltyCharge(Request $request, $id_user)
    {
        $old_balance = UserBalance::where('id_user', $id_user)->pluck('balance')->first();

        $new_balance = $old_balance - 20000;

        $input = [
            'balance' =>  $new_balance,
            ];

        if($new_balance < 0)
        {
            return response()->json(['error' => 'Insufficient balance'], 402);
        }            

        UserBalance::where('id_user', $id_user)->update($input);

        return response()->json(['msg'=>'Success']);
    }

    public function additionalCharge(Request $request, $id_user)
    {
        $old_balance = UserBalance::where('id_user', $id_user)->pluck('balance')->first();

        $new_balance = $old_balance - 1500;

        $input = [
            'balance' =>  $new_balance,
            ];

        if($new_balance < 0)
        {
            return response()->json(['error' => 'Insufficient balance'], 402);
        }            

        UserBalance::where('id_user', $id_user)->update($input);

        return response()->json(['msg'=>'Success']);
    }
}
