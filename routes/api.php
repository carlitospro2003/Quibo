<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\MessageController;
use App\Http\Controllers\EncryptionController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
hola
*/
Route::get('/', function (Request $request) {
    return (object)['Hello'=>'World'];
});

Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group([
    'middleware' => ['api'],
    'prefix' => 'user'
], function ($router) {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});


Route::prefix('message')->group(function () {
    //Obtiene mensajes
    Route::get('get-messages', [MessageController::class, 'getMessages']);
    //Sube mensaje
    Route::post('post-message', [MessageController::class, 'postMessage']);
});