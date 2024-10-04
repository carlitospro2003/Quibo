<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
}
