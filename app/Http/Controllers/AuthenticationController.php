<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

    public function addInvestor(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'brand_name' => 'required|string',
            'brand_image' => 'required|mimes:png,jpg,jpeg,gif,jfif,svg|max:2048',
            'brand_info' => 'required|string',
            'expo_id' => 'required|exists:expos,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('name', 'INVESTOR')->first()->id
        ]);

        $brand = Brand::create([
            'name' => $request->brand_name,
            'info' => $request->brand_info,
            'expo_id' => $request->expo_id,
            'user_id' => auth()->user()->id
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '-' . $image->getClientOriginalName();
            Storage::disk('public')->put('/brand-images' . '/' . $fileName, File::get($image));
            $brand->image = '/brand-images' . '/' .  $fileName;
            $brand->save();
        }

        return response()->json([
            'data' => null,
            'message' => 'User created successfully'
        ], 200);
    }
}
