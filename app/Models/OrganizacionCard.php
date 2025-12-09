<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizacionCard extends Model
{
    protected $table = 'organizacion_card';

    protected $fillable = [
        'nombres',
        'codigo',
        'cargo',
        'especialidad',
        'email',
        'telefono',
        'ruta_imagen',
        'ruta_cv',
        'grupo',
        'orden',
        'activo'
    ];
}