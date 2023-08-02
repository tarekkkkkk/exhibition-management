<?php

namespace App\Http\Controllers\Expo;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::select('id', 'name', 'info', 'expo_id', 'image')->get();

        $brandsWithUrls = $brands->map(function ($item, $key) {
            $item->image = url('/storage' . $item->image);
            return $item;
        });

        return response()->json([
            'data' => $brandsWithUrls,
            'status' => 'success'
        ], 200);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'info' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,gif,jfif,svg|max:2048',
            'expo_id' => 'required|exists:expos,id'
        ]);

        if (auth()->user()->brand()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You already have a brand'
            ], 422);
        }

        $brand = Brand::create([
            'name' => $request->name,
            'info' => $request->info,
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
            'message' => 'Brand has been added succefully',
            'status' => 'success',
            'data' => collect([
                'id' => $brand->id,
                'name' => $brand->name,
                'info' => $brand->info,
                'expo_id' => (int)$brand->expo_id,
                'image' => url('storage/' . $brand->image)
            ])
        ], 201);
    }


    public function show(string $id)
    {
        $brand = Brand::find($id);
        if ($brand == true) {
            return response()->json([
                'status' => 'success',
                'data' => collect([
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'info' => $brand->info,
                    'expo_id' => (int)$brand->expo_id,
                    'image' => url('storage/' . $brand->image)
                ])
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Post not found'
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {

        $validatedData = $request->validate([
            'expo_id' => 'prohibited'
        ]);

        $brand = Brand::find($id);

        if ($brand) {
            $brand->update($request->all());

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '-' . $image->getClientOriginalName();

                Storage::disk('public')->put('/brand-images' . '/' . $fileName, File::get($image));

                $brand->image = '/brand-images' . '/' .  $fileName;
                $brand->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Brand updated successfully',
                'data' => collect([
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'info' => $brand->info,
                    'expo_id' => (int)$brand->expo_id,
                    'image' => url('storage/' . $brand->image)
                ])
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $brand = Brand::find($id);

        if ($brand) {
            $brand->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Brand deleted successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found'
            ], 404);
        }
    }

    public function     myBrand()
    {
        $brand = auth()->user()->brand;
        $brand->image = url('storage/' . $brand->image);

        return response()->json([
            'data' => $brand
        ]);
    }
}
