<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

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
], function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user',    [AuthController::class, 'getAuthenticatedUser']);
    });
});

// Ruta para registrar un nuevo usuario
Route::post('/register', [RegisterController::class, 'register']);

// Ruta para iniciar sesión
Route::post('/login', [LoginController::class, 'login']);

// Ruta para cifrar el mensaje
Route::post('/encrypt-message', [EncryptionController::class, 'encryptMessage']);

// Ruta para descifrar el mensaje
Route::post('/decrypt-message', [EncryptionController::class, 'decryptMessage']);

Route::prefix('message')->group(function () {
    
    //Obtiene mensajes
    Route::get('get-messages', [MessageController::class, 'getMessages']);
    //Middleware de auth
    Route::middleware(['auth:sanctum', 'activeaccount'])->group(function () {
        //Sube mensaje encriptado a mongodb   
        Route::post('post-message', [MessageController::class, 'postMessage']);
    });
});