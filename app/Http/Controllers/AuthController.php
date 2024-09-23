<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credenciales = $request->only('email', 'password');

        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();

            return response()->json(['mensaje' => 'Inicio de sesión exitoso']);
        }

        return response()->json(['error' => 'Credenciales incorrectas'], 401);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:4|confirmed',
        ]);

        $usuario = User::create([
            'username' => $request->username,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($usuario);

        return response()->json(['mensaje' => 'Registro exitoso']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['mensaje' => 'Sesión cerrada']);
    }

    public function getAuthenticatedUser(Request $request)
    {
        return response()->json($request->user());
    }
}
