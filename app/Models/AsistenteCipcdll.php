<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenteCipcdll extends Model
{
    protected $table = 'asistentes_cipcdll';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'cip',
        'nombres',
        'apellidos',
        'capitulo',
        'dni',
        'celular',
        'correo',
        'estado'
    ];
}