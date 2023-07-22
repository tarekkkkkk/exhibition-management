<?php

namespace App\Http\Controllers\Expo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expo;
use App\Models\Favourite;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::select(['id', 'name', 'price', 'image', 'brand_id'])->get();

        $productsWithUrls = $products->map(function ($item, $key) {
            $item->image = url('/storage' . $item->image);
            $item->is_added_to_favourite = Favourite::where('product_id', $item->id)->where('user_id', auth()->user()->id)->first() ? true : false;
            return $item;
        });

        return response()->json([
            'data' => $productsWithUrls,
            'status' => 'success'
        ], 200);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required',
            'image' => 'required|mimes:png,jpg,jpeg,gif,jfif,svg|max:2048',
            'brand_id' => 'required|exists:brands,id'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'brand_id' => $request->brand_id
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '-' . $image->getClientOriginalName();

            Storage::disk('public')->put('/product-images' . '/' . $fileName, File::get($image));

            $product->image = '/product-images' . '/' .  $fileName;
            $product->save();
        }

        return response()->json([
            'message' => 'Products has been added successfully',
            'status' => 'success',
            'data' => collect([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'brand_id' => $product->brand_id,
                'image' => url('storage/' . $product->image)
            ])
        ], 201);
    }
    public function show(string $id)
    {
        $product = Product::find($id);
        if ($product == true) {
            return response()->json([
                'status' => 'success',
                'data' =>  collect([
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'brand_id' => $product->brand_id,
                    'image' => url('storage/' . $product->image)
                ])
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
        $validatedData = $request->validate([
            'brand_id' => 'prohibited'
        ]);
        $product = product::find($id);

        if ($product) {

            $product->update($request->all());

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '-' . $image->getClientOriginalName();

                Storage::disk('public')->put('/product-images' . '/' . $fileName, File::get($image));

                $product->image = '/product-images' . '/' .  $fileName;
                $product->save();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'product updated successfully',
                'data' =>  collect([
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (int)$product->price,
                    'brand_id' => (int)$product->brand_id,
                    'image' => url('storage/' . $product->image)
                ])
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


    public function addToFavourite(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        if ($favourite = Favourite::where('product_id', $request->product_id)->where('user_id', auth()->user()->id)->first()) {
            $favourite->delete();
            return response()->json([
                'status' => 'sucess',
                'message' => 'product removed favourite'
            ], 200);
        }
        Favourite::create([
            'user_id' => auth()->user()->id,
            'product_id' => $request->product_id
        ]);

        return response()->json([
            'status' => 'sucess',
            'message' => 'product added to favourite'
        ], 200);
    }

    public function favouriteProducts()
    {
        // $favourites = Favourite::where('user_id', auth()->user()->id)->get();
        // $products = [];

        // foreach ($favourites as $favourite) {
        //     $product = $favourite->product;
        //     $product->image = url('storage/' . $product->image);
        //     $products[] = $product;
        // }

        $products = auth()->user()->products->map(function ($item) {
            $item->image = url('storage/' . $item->image);
            return $item;
        });

        return response()->json([
            'message' => 'All products in favourite',
            'data' => $products
        ], 200);
    }
}
