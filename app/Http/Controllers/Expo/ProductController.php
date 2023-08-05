<?php

namespace App\Http\Controllers\Expo;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddProductRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Models\Expo;
use App\Models\Favourite;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::select(['id', 'name', 'price', 'info', 'image'])->get();

        $productsWithUrls = $products->map(function ($item, $key) use ($request) {
            $item->image = url('/storage' . $item->image);
            $item->is_added_to_favourite = Favourite::where('product_id', $item->id)->where('user_id',  $request->user('sanctum')?->id) ? true : false;
            return $item;
        });

        return response()->json([
            'data' => $productsWithUrls,
            'status' => 'success'
        ], 200);
    }
    public function store(AddProductRequest $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'price' => 'required',
            'info' => 'required',
            'image' => 'required',
            'expo_id' => 'required|exists:expos,id'
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'info' => $request->info,
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '-' . $image->getClientOriginalName();

            Storage::disk('public')->put('/product-images' . '/' . $fileName, File::get($image));

            $product->image = '/product-images' . '/' .  $fileName;
            $product->save();
        }

        $brand = auth()->user()->brand;
        $expo = Expo::find($request->expo_id);
        $brand_expo = $brand->brandExpo()->where('expo_id', $expo->id)->first();
        $product->brand_expo_id = $brand_expo->id;
        $product->save();

        return response()->json([
            'message' => 'Products has been added successfully',
            'status' => 'success',
            'data' => collect([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'info' => $product->info,
                'brand_id' => $product->brand_id,
                'image' => url('storage/' . $product->image)
            ])
        ], 201);
    }
    public function show(Request  $request, string $id)
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
                    'info' => $product->info,
                    'is_added_to_favourite' => Favourite::where('product_id',  $product->id)->where('user_id',  $request->user('sanctum')->id)->exists() ? true : false,
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
                    'info' => (int)$product->info,
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
    public function productInBrandInExpo(Request $request, Expo $expo, Brand $brand)
    {
        $brandExpo = $expo->brandExpo()?->where('brand_id', $brand->id)->first();

        $products =  $brandExpo->products->map(function ($item) use ($request) {
            $item->image = url('storage/' . $item->image);
            if ($request->user('sanctum')?->role?->name == "INVESTOR") {
                if ($item->brandExpo->brand->id == $request->user('sanctum')?->brand?->id) {
                    $item->is_owned = true;
                } else {
                    $item->is_owned = false;
                }
            } else {
                $item->is_owned = false;
            }
            $item->is_added_to_favourite = Favourite::where('product_id', $item->id)->where('user_id',  $request->user('sanctum')?->id)->exists() ? true : false;
            return $item;
        });

        return response()->json([
            'message' => 'All products in brand',
            'data' => $brandExpo->products,
        ], 200);
    }
}
