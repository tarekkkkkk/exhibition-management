<?php

namespace App\Http\Controllers\Expo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expo;
use PhpParser\Node\Stmt\Foreach_;

class ExpoController extends Controller
{
    public function index()
    {
        $expo = Expo::all();

        return response()->json([

            'info' => $expo,
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

        $item = new Expo();
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
            'message' => 'Items has been added succefully',
            'status' => 'success',
            'Data' => $item
        ], 200);
      }
    }
    public function show(string $id)
    {
        $Expo = Expo::find($id);
        if ($Expo == true) {
            return response()->json([
                'status' => 'success',
                'data' => $Expo
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
        $expo = expo::find($id);

        if ($expo) {
            $expo->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'expo updated successfully',
                'data' => $expo
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
}
