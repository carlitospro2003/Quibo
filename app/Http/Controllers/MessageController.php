<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Chat;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    //
    /**
     * Encripta un mensaje y lo devuelve en la respuesta.
     */
    public function encryptMessage(Request $request)
    {
        // Validar que el campo "message" esté presente en la solicitud
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',  // El mensaje a encriptar
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Encriptar el mensaje usando SHA-256
        $encryptedMessage = hash('sha256', $request->message);

        // Devolver el mensaje encriptado en la respuesta
        return response()->json([
            'original_message' => $request->message,
            'encrypted_message' => $encryptedMessage,
        ], 200);
    }

    // Obtener datos de MongoDB Durann23
    public function getMessages()
    {
        $mensajes = Chat::orderBy('fecha', 'asc')->get();
        $mensajesDesencriptados = $mensajes->map(function($mensaje) {
            $mensaje->mensajencriptado = Crypt::decryptString($mensaje->mensajencriptado);
            return $mensaje;
        });
        return response()->json($mensajesDesencriptados);    
    }
    //Subir mensaje
    public function postMessage($request)
    {
        $validador = Validator::make($request->all(), [
            "message" => "required|string",
        ]);
        if ($validador->fails()) {
            return response()->json([
                'result' => false,
                'msg' => "Los datos no cumplen.",
                'error' => $validador->errors()
            ], 400);
        }
        $mensaje = $request->message;
        // Encriptar el mensaje antes de guardarlo
        $mensajeEncriptado = Crypt::encryptString($mensaje);
        $user = Auth::user();
        // Guardar el mensaje en MongoDB
        Chat::create([
            'mensajencriptado' => $mensajeEncriptado,
            'usuario_id' => $user->id,
            'fecha' => now(),
        ]);

        return response()->json([
            'result' => true,
            'msg' => 'Mensaje encriptado y guardado'
        ], 200);

    }
}
