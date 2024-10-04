<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class EncryptionController extends Controller
{
    //
    /**
     * Cifra un mensaje usando el sistema de cifrado de Laravel (AES-256).
     */
    public function encryptMessage(Request $request)
    {
        // Validar que el campo "message" esté presente
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Cifrar el mensaje
        $encryptedMessage = Crypt::encryptString($request->message);

        // Devolver el mensaje cifrado
        return response()->json([
            'original_message' => $request->message,
            'encrypted_message' => $encryptedMessage,
        ], 200);
    }

    /**
     * Descifra un mensaje cifrado usando el sistema de cifrado de Laravel (AES-256).
     */
    public function decryptMessage(Request $request)
    {
        // Validar que el campo "encrypted_message" esté presente
        $validator = Validator::make($request->all(), [
            'encrypted_message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            // Descifrar el mensaje
            $decryptedMessage = Crypt::decryptString($request->encrypted_message);

            // Devolver el mensaje descifrado
            return response()->json([
                'encrypted_message' => $request->encrypted_message,
                'decrypted_message' => $decryptedMessage,
            ], 200);
        } catch (\Exception $e) {
            // Si ocurre un error en el descifrado
            return response()->json(['error' => 'Invalid encrypted message'], 400);
        }
    }
}
