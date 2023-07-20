<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\User;
use Dflydev\DotAccessData\Data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use  Laravel\Sanctum\NewAccessToken;


class TestController extends Controller
{

    public function Register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->save();
        return response()->json([
            'message' => 'User has been Registered successfully'
        ], 200);
    }



    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required|string'
        ]);

        $user = User::where('email',   $request->email)->first();
        // dd($request->password);
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $tokenResult = $user->createToken('token')->plainTextToken;
                return response()->json([
                    'data' => ['access_token' => $tokenResult],
                    'User' => $user,
                    'Token'=> $tokenResult,
                    'status' => 200,
                    'message' => 'Login Successfuly'
                ]);
            } else {
                return Response()->json(['message' => 'worng Password'], 401);
            }
        } else {
            return Response()->json(['message' => 'email not found'], 401);
        }
    }
    //email not found


    // $user = $request->user();
    // $tokenResult = $user->createToken('Personal Access Token');
    // $token = $tokenResult->token;
    // $token->expires_at = Carbon::now()->addweeks(1);
    // $token->save();










































































    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Test = Test::latest()->paginate(4);
        // return view('index', compact('Test'));
        return response()->json(
            [
                'data' => $Test,
                'Req_Date' => date('D,M,Y'),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // return [
        //     'id' => $this->$id,
        //     'name' => $this->name,
        //     'email' => $this->email,
        //     'created_at' => $this->created_at,
        //     'updated_at' => $this->updated_at,
        // ];



        //    dd('safdds');
        $Data = Test::create($request->all());
        // $Data = new Test;
        $Data->name = $request->name;
        $Data->email = $request->email;
        $Data->save();
        return response()->json(['message' => 'Fields created successfully']);
        // , 'Data'    => $Data]);



        // $request->validate([
        //     'name' => 'required',
        //     'Descriptions' => 'required',
        //     'status' => 'required'
        // ]);
        // $Data = Test::create($request->all());
        // return response()->json([$Data,'message' => 'Data Created Successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Test $test)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Test $test)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Test $test)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Test $test)
    {
        //
    }
}
