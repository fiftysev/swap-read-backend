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
    Route::get('', 'BooksController@index');
    Route::get('{id}', 'BooksController@show');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('create', 'BooksController@store');
        Route::post('{id}', 'BooksController@update');
        Route::post('rate/{id}', 'BooksController@rate');
    });

    Route::delete('{id}', 'BooksController@destroy');
});

Route::prefix('follow_service')->middleware('auth:sanctum')->group(function (){
    Route::get('', 'SubscriptionsController@get_followers');
});
