<?php

use App\Http\Controllers\SpotifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::post('/v1/spotify/oauth', function (Request $request) {
//     return $request->user();
// });

// $router->group(['prefix' => 'v1'], function () use ($router) {
//     // Device
//     // $router->post("/spotify/oauth", "SpotifyController@oAuth");
//     $router->post("/spotify/oauth", [SpotifyController::class, 'oAuth']);

//     // $router->post("/payment/event", "PaymentController@events");
// });


Route::prefix('v1')->group(function () {
    // Route::post('/spotify/oauth', 'SpotifyController@oAuth');
    Route::get("/spotify/auth/{code}", [SpotifyController::class, 'generateTokenByAuthCode']);

});