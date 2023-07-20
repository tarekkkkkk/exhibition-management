<?php

namespace App\Http\Controllers\Expo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expo;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $Product = Product::all();

        return response()->json([

            'info' => $Product,
            'status' => 'success'
        ], 200);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required',
            // 'image' => 'required|string',
        ]);

        $item = new Product();
        $item->name = $validatedData['name'];
        $item->price = $validatedData['price'];
        // $store->image = $validatedData['image'];

        $item->save();
        return response()->json([
            'message' => 'Products has been added successfully',
            'status' => 'success',
            'Data' => $item
        ], 200);
    }
    public function show(string $id)
    {
        $product = Product::find($id);
        if ($product == true) {
            return response()->json([
                'status' => 'success',
                'data' => $product
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'product not found'
            ], 404);
        }
    }
    public function update(Request $request, $id)
    {
        $product = product::find($id);

        if ($product) {
            $product->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'product updated successfully',
                'data' => $product
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'product not found'
            ], 404);
        }
    }
    public function destroy($id)
    {
        $product = product::find($id);

        if ($product) {
            $product->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'product deleted successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'product not found'
            ], 404);
        }
    }
}
