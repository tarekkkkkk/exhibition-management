<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\Expo\ExpoController;
use App\Http\Controllers\Expo\ProductController;
use App\Http\Controllers\Expo\BrandController;
use Illuminate\Support\Facades\Route;

Route::group(

   ['prefix' => 'Auth'],

   function(){
    Route::post('Reg', [TestController::class,'Register']);
    Route::post('login', [TestController::class,'Login']);

    // Route::post('login', 'TestController@Login')->name('Login');
   }
);

// #Routers for EXPO
Route::post('store', [ExpoController::class,'store']);
Route::post('show/{id}', [ExpoController::class,'show']);
Route::post('index', [ExpoController::class,'index']);
Route::post('edit/{id}', [ExpoController::class,'update']);
Route::post('delete/{id}', [ExpoController::class,'destroy']);


// #Routers To Buy things
Route::post('product-store', [ProductController::class,'store']);
Route::post('product-show/{id}', [ProductController::class,'show']);
Route::post('product-index', [ProductController::class,'index']);
Route::post('product-edit/{id}', [ProductController::class,'update']);
Route::post('product-delete/{id}', [ProductController::class,'destroy']);

// #Routers About Brands
Route::post('Brand-store', [BrandController::class,'store']);
Route::post('Brand-show/{id}', [BrandController::class,'show']);
Route::post('Brand-index', [BrandController::class,'index']);
Route::post('Brand-edit/{id}', [BrandController::class,'update']);
Route::post('Brand-delete/{id}', [BrandController::class,'destroy']);
