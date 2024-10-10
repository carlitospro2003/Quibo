<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class VerifyJwtToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Verificar el token y autenticar al usuario
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // Si el token ha expirado
            return response()->json(['error' => 'Token expirado'], 401);
        } catch (TokenInvalidException $e) {
            // Si el token es inválido
            return response()->json(['error' => 'Token invalido'], 401);
        } catch (Exception $e) {
            // Si no se ha encontrado el token
            return response()->json(['error' => 'Token no encontrado'], 401);
        }

        return $next($request);
    }
}
