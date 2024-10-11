<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
    function reverseStringHalves($string) {
        // Obtener la longitud del string
        $length = strlen($string);
    
        // Calcular la mitad
        $half = intval($length / 2);
    //hola laho ohal
    //ohal aloh hola
        // Dividir el string en dos mitades
        $firstHalf = substr($string, 0, $half);
        $secondHalf = substr($string, $half);
        // Invertir las mitades
        return strrev($secondHalf . $firstHalf);
    }
    // Obtener datos de MongoDB Durann23
    public function getMessages()
    {
        try {
            // Obtener todos los mensajes de la colección de MongoDB
            $mensajes = Chat::orderBy('fecha', 'asc')->get();

            // Desencriptar los mensajes
            $mensajesDesencriptados = $mensajes->map(function ($mensaje) {

                // Desencriptar el campo message
                $mensaje->message = Crypt::decryptString($this->reverseStringHalves($mensaje->message));
                $mensaje->makeHidden(['created_at', 'updated_at']);
                return $mensaje; // Retornar el mensaje con el campo desencriptado
            });

            // Retornar los mensajes en formato JSON
            return response()->json([
                'result' => true,
                'msg' => "Mensajes obtenidos.",
                'data' => $mensajesDesencriptados
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los mensajes: ' . $e->getMessage()], 500);
        }
    }

    //Subir mensaje
    public function postMessage(Request $request)
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
        $messageinvert = $this->reverseStringHalves($mensajeEncriptado);
        $user = auth()->user();
        Chat::create([
            'message' => $messageinvert,
            'usuario_id' => $user->id,
            'userName'=> $user->name,
            'fecha' => now(),
            'time' => Carbon::now('America/Monterrey')->format('g:i A'), // Hora y minutos en formato 12h con AM/PM

        ]);

        return response()->json([
            'result' => true,
            'msg' => 'Mensaje encriptado y guardado'
        ], 200);
    }
}
