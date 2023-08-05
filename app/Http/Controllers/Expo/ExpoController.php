<?php

namespace App\Http\Controllers\Expo;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Expo;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ExpoController extends Controller
{
    public function index(Request $request)
    {
        $expos = Expo::select('id', 'name', 'info', 'image', 'user_id', 'address')->get();

        $exposWithUrls = $expos->map(function ($item, $key) use ($request) {
            if ($request->user('sanctum')?->role->name == "OWNER") {
                if ($item->user->id == $request->user('sanctum')?->id) {
                    $item->is_owned = true;
                } else {
                    $item->is_owned = false;
                }
            } else {
                $item->is_owned = false;
            }
            $item->image = url('/storage' . $item->image);
            return $item;
        });

        return response()->json([
            'data' => $exposWithUrls,
            'status' => 'success'
        ], 200);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'info' => 'required',
            'image' => 'required',
            'address' => 'required|string'
        ]);

        $expo = Expo::create([
            'name' => $request->name,
            'info' => $request->info,
            'user_id' => auth()->user()->id,
            'address' => $request->address
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '-' . $image->getClientOriginalName();

            Storage::disk('public')->put('/expo-images' . '/' . $fileName, File::get($image));

            $expo->image = '/expo-images' . '/' .  $fileName;
            $expo->save();
        }

        return response()->json([
            'data' => collect([
                'id' => $expo->id,
                'name' => $expo->name,
                'info' => $expo->info,
                'image' => url('storage/' . $expo->image),
                'address' => $expo->address
            ]),
            'message' => 'Items has been added succefully',
        ], 201);
    }
    public function show(string $id)
    {
        $expo = Expo::findOrFail($id);
        if ($expo == true) {
            return response()->json([
                'status' => 'success',
                'data' =>  collect([
                    'id' => $expo->id,
                    'name' => $expo->name,
                    'info' => $expo->info,
                    'address' => $expo->address,
                    'image' => url('storage/' . $expo->image),
                ]),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Expo not found'
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $expo = expo::find($id);

        if ($expo) {
            $expo->update($request->all());
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '-' . $image->getClientOriginalName();

                Storage::disk('public')->put('/expo-images' . '/' . $fileName, File::get($image));

                $expo->image = '/expo-images' . '/' .  $fileName;
                $expo->save();
            }
            return response()->json([
                'status' => 'success',
                'message' => 'expo updated successfully',
                'data' =>  collect([
                    'id' => $expo->id,
                    'name' => $expo->name,
                    'info' => $expo->info,
                    'image' => url('storage/' . $expo->image),
                    'address' => $expo->address
                ]),
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'expo not found'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $expo = Expo::find($id);

        if ($expo) {
            $expo->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'expo deleted successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'expo not found'
            ], 404);
        }
    }

    public function expoBrands($id, Request $request)
    {
        $expo = Expo::findOrFail($id);

        // $brands = Brand::where('expo_id', $expo->id)->select('id', 'name', 'info', 'image')->get();
        $brands = $expo->brands;

        $brandsWithUrls = $brands->map(function ($item, $key) use ($request) {
            if ($request->user('sanctum')?->role->name == "INVESTOR") {
                if ($item->user_id == $request->user('sanctum')?->id) {
                    $item->is_owned = true;
                } else {
                    $item->is_owned = false;
                }
            } else {
                $item->is_owned = false;
            }
            $item->image = url('/storage' . $item->image);
            return $item;
        });

        return response()->json([
            'data' => $brandsWithUrls,
            'status' => 'success'
        ], 200);
    }
    public function existingInvestor(Expo $expo, Brand $brand)
    {
        $expo->brands()->syncWithoutDetaching($brand->id);
        return response()->json([
            'data' => null,
            'message' => 'brand added to your expo successfully'
        ], 200);
    }
}
