<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    # Ver mensajes entre dos usuarios
    public function index($userId)
    {
        // Obtener los mensajes entre el usuario autenticado y el usuario destinatario
        $messages = ChatMessage::where(function ($query) use ($userId) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', Auth::id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    # Enviar un mensaje a otro usuario
    public function store(Request $request, $userId)
    {
        // Validar el contenido del mensaje
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);
                    // Crear el mensaje
        $message = ChatMessage::create([
            'sender_id' => Auth::id(),  // El usuario autenticado es el remitente
            'receiver_id' => $userId,   // El destinatario es el usuario pasado por parámetro
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }
}
