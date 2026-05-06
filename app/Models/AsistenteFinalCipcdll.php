<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsistenteFinalCipcdll extends Model
{
    protected $table = 'asistentes_cipcdll_filtrado';

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
        'estado',
        'asistio'
    ];
}
