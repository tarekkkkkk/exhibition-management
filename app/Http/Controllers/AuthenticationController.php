<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddInvestorRequest;
use App\Models\Brand;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PharIo\Manifest\Author;
// use Illuminate\Foundation\Http\FormRequest;
use app\Http\Requests\NewFormRequest;
// use Illuminate\Http\request;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\NewFormRequest\adminAuth;
use Illuminate\Support\Facades\Password;



class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'owner_code' => 'integer'
        ]);
        // $u = User::all();
        if ($request->ownr_code == 4) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => Role::where('name', 'OWNER')->first()->id
            ]);
        }
        else{
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
                    'message' => 'Worng Password'
                ], 422);
            }
        } else {
            return Response()->json([
                'data' => null,
                'message' => 'email not found'
            ], 404);
        }
    }

    public function addInvestor(AddInvestorRequest $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password',
            'brand_name' => 'required|string',
            'brand_image' => 'required',
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
            'user_id' => $user->id
        ]);

        $brand->expos()->sync($request->expo_id);

        if ($request->hasFile('brand_image')) {
            $image = $request->file('brand_image');
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
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required|string|min:6|same:password',
            'token' => 'required'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        dd($request->$status);

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset successfully']);
        } else {
            return response()->json(['message' => 'Password reset failed'], 422);
        }
    }
}
