<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post( 'register', [UserController::class, 'user_register'] );
Route::post( '/login', [UserController::class, 'login'] );

Route::middleware('auth:api')->group( function(){
    Route::get( 'userDetails', [UserController::class, 'userDetails'] );
    Route::post( 'logout', [UserController::class, 'logout'] );
    //product
    Route::post( 'createProduct', [ProductController::class, 'store'] );
    Route::get( 'getProducts', [ProductController::class, 'index'] );
} );

Route::get( 'getAllProducts', [ProductController::class, 'get_all_products'] );
