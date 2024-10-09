<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'messages';

    protected $fillable = [
        'sender_id',     // ID del remitente
        'receiver_id',   // ID del destinatario
        'message',       // Contenido del mensaje
    ];

    // Relación con el remitente (usuario que envió el mensaje)
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Relación con el destinatario (usuario que recibió el mensaje)
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

}
