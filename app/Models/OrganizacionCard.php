<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizacionCard extends Model
{
    protected $table = 'organizacion_card';

    protected $fillable = [
        'nombres',
        'cargo',
        'email',
        'telefono',
        'ruta_imagen',
        'grupo',
        'orden',
        'activo'
    ];
}