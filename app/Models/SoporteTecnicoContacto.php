<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoporteTecnicoContacto extends Model
{
    protected $table = 'soporte_tecnico_contactos';
    public $timestamps = false;

    protected $fillable = [
        'nombres',
        'numero_contacto',
        'correo_electronico',
        'estado'
    ];
}
