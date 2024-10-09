<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use MongoDB\Laravel\Eloquent\Model;

class Chat extends Model 
{
    use Notifiable;

    protected $connection = 'mongodb';
    protected $collection = 'chats';

    protected $fillable = [
        'message',
        'usuario_id',
        'fecha',
    ];
}
