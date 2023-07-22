<?php

namespace App\Http\Controllers;

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
            'password' => 'required|string|min:6'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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
                return response()->json([
                    'data' => ['access_token' => $tokenResult],
                    'user' => $user,
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
