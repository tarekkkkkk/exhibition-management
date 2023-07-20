<?php

namespace App\Http\Controllers\Expo;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $Brand = Brand::all();

        return response()->json([

            'info' => $Brand,
            'status' => 'success'
        ], 200);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'info' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,gif,jfif,svg|max:2048',
        ]);

        $item = new Brand();
        $item->name  = $validatedData['name'];
        $item->info  = $validatedData['info'];
        $item->image = $validatedData['image'];
        $item->save();

        if ($request->has('image')) {
            $image = $request->image;

            foreach ($image as $key => $value) {
                $name = time() . $key . '.' . $value->getClientOrginalExtention();

                $path = public_path('upload');

                $image->move($path, $name);
            }   
        return response()->json([
            'message' => 'Brand has been added succefully',
            'status' => 'success',
            'Data' => $item
        ], 200);
      }
    }
    public function show(string $id)
    {
        $Brand = Brand::find($id);
        if ($Brand == true) {
            return response()->json([
                'status' => 'success',
                'data' => $Brand
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
        $Brand = Brand::find($id);

        if ($Brand) {
            $Brand->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Brand updated successfully',
                'data' => $Brand
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
        $Brand = Brand::find($id);

        if ($Brand) {
            $Brand->delete();

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
}
