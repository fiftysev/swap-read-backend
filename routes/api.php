<?php

use App\Http\Controllers\AuthController;
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

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('', [AuthController::class, 'whoAmI']);
        Route::get('profile', 'UserController@profile');
    });
});

Route::prefix('books')->group(function () {
    Route::get('', 'BookController@index');
    Route::get('{id}', 'BookController@show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', 'BookController@store');
        Route::post('{id}', 'BookController@update');
    });

    Route::delete('{id}', 'BookController@destroy');
});

Route::prefix('reviews')->group(function (){
    Route::get('{id}', 'ReviewController@index');
    Route::post('{id}', 'ReviewController@store')->middleware('auth:sanctum');
    Route::delete('{id}', 'ReviewController@destroy')->middleware('auth:sanctum');
});

Route::prefix('subscription')->middleware('auth:sanctum')->group(function (){
    Route::get('followers', 'SubscriptionsController@followers');
    Route::get('follows', 'SubscriptionsController@follows');
    Route::post('follow/{id}', 'SubscriptionsController@store');
    Route::delete('unfollow/{id}', 'SubscriptionsController@destroy');
});
