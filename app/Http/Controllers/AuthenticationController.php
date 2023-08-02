<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('name', 'USER')->first()->id
            // 'confirm_password' => $request->confirm_password    
        ]);

        return response()->json([
            'data' => $user,
            'message' => 'User has been Registered successfully'
        ], 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required|string'
        ]);

        $user = User::where('email',   $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                $tokenResult = $user->createToken('token')->plainTextToken;
                $user->load('role');
                return response()->json([
                    'data' => [
                        'user' => $user,
                    ],
                    'token' => $tokenResult,
                    'status' => 200,
                    'message' => 'Login Successfuly'
                ]);
            } else {
                return Response()->json([
                    'data' => null,
                    'message' => 'worng Password'
                ], 422);
            }
        } else {
            return Response()->json([
                'data' => null,
                'message' => 'email not found'
            ], 404);
        }
    }
}
