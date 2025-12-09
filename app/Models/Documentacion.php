<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentacion extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_publicacion',
        'seccion',
        'categoria',
        'ruta_archivo',
        'ruta_miniatura',
        'activo'
    ];

    protected $casts = [
        'fecha_publicacion' => 'date',
        'activo' => 'boolean',
    ];
}