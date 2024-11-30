<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Contracts\Providers\JWT;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $passwordHash = hash('sha256', $request->password);
        Log::info($passwordHash);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' =>$passwordHash,  // Encriptamos la contraseña
        ]);

        return response()->json([
            'message' => 'Usuario registrado correctamente.',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::where('email', $request->email)->first();
        Log::info($user);
        Log::info($user->password);
        if ($user) {
            $passwordHash = hash('sha256', $request->password);
            Log::info($passwordHash);
            // Comparar los hashes
            if ($user->password === $passwordHash) {
                $token = auth()->login($user);  // Generar el token manualmente usando el usuario
                return response()->json([
                    'result' => true,
                    'msg' => "Inicio de sesión exitoso.",
                    'access_token' => $token,
                ], 200);
            }
            return response()->json([
                'result' => false,
                'msg' => "Contraseña incorrecta."
            ], 404);
        }
        return response()->json([
            'result' => false,
            'msg' => "Cuenta no existente."
        ], 404);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

}
