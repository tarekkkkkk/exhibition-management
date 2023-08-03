<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Expo\ExpoController;
use App\Http\Controllers\Expo\ProductController;
use App\Http\Controllers\Expo\BrandController;
use Illuminate\Support\Facades\Route;

Route::group(

   ['prefix' => 'auth'],

   function () {
      Route::post('reg', [AuthenticationController::class, 'register']);
      Route::get('login', [AuthenticationController::class, 'login']);
   }
);

Route::get('expos', [ExpoController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('brands', [BrandController::class, 'index']);
Route::get('expos/{id}/brands', [ExpoController::class, 'expoBrands']);

Route::middleware(['auth:sanctum'])->group(function () {
   Route::post('auth/add-investor', [AuthenticationController::class, 'addInvestor']);

   // #Routers for EXPO
   Route::post('expos', [ExpoController::class, 'store']);
   Route::get('expos/{id}', [ExpoController::class, 'show']);
   Route::put('expos/{id}', [ExpoController::class, 'update']);
   Route::delete('expos/{id}', [ExpoController::class, 'destroy']);
   Route::post('/expos/{expo}/brands/{brand}/add-investor', [ExpoController::class, 'existingInvestor']);



   // #Routers To Buy things
   Route::post('products', [ProductController::class, 'store']);
   Route::get('products/{id}', [ProductController::class, 'show']);
   Route::put('products/{id}', [ProductController::class, 'update']);
   Route::delete('products/{id}', [ProductController::class, 'destroy']);
   Route::get('fav-products', [ProductController::class, 'favouriteProducts']);
   Route::get('/expos/{expo}/brands/{brand}/products', [ProductController::class, 'productInBrand']);

   // #Routers About Brands
   Route::post('brands', [BrandController::class, 'store']);
   Route::get('brands/{id}', [BrandController::class, 'show']);
   Route::put('brands/{id}', [BrandController::class, 'update']);
   Route::delete('brands/{id}', [BrandController::class, 'destroy']);
   Route::get('my-brand', [BrandController::class, 'myBrand']);


   Route::post('/add-to-fav', [ProductController::class, 'addToFavourite']);
});
