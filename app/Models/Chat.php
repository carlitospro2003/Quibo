<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user')->withTimestamps();
    }
}
/*
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Mensaje extends Eloquent
{
    protected $connection = 'mongodb'; // Define que este modelo usa MongoDB
    protected $collection = 'mensajes'; // El nombre de tu colección
}

*\