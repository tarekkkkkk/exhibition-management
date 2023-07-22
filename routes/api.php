<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Expo\ExpoController;
use App\Http\Controllers\Expo\ProductController;
use App\Http\Controllers\Expo\BrandController;
use Illuminate\Support\Facades\Route;

Route::group(

   ['prefix' => 'Auth'],

   function () {
      Route::post('Reg', [AuthenticationController::class, 'register']);
      Route::get('login', [AuthenticationController::class, 'login']);
   }
);

Route::middleware(['auth:sanctum'])->group(function () {
   // #Routers for EXPO
   Route::post('expos', [ExpoController::class, 'store']);
   Route::get('expos/{id}', [ExpoController::class, 'show']);
   Route::get('expos', [ExpoController::class, 'index']);
   Route::put('expos/{id}', [ExpoController::class, 'update']);
   Route::delete('expos/{id}', [ExpoController::class, 'destroy']);
   Route::get('expos/{id}/brands', [ExpoController::class, 'expoBrands']);


   // #Routers To Buy things
   Route::post('products', [ProductController::class, 'store']);
   Route::get('products/{id}', [ProductController::class, 'show']);
   Route::get('products', [ProductController::class, 'index']);
   Route::put('products/{id}', [ProductController::class, 'update']);
   Route::delete('products/{id}', [ProductController::class, 'destroy']);
   Route::get('fav-products', [ProductController::class, 'favouriteProducts']);

   // #Routers About Brands
   Route::post('brands', [BrandController::class, 'store']);
   Route::get('brands/{id}', [BrandController::class, 'show']);
   Route::get('brands', [BrandController::class, 'index']);
   Route::put('brands/{id}', [BrandController::class, 'update']);
   Route::delete('brands/{id}', [BrandController::class, 'destroy']);
   Route::get('my-brand', [BrandController::class, 'myBrand']);


   Route::post('/add-to-fav', [ProductController::class, 'addToFavourite']);
});
