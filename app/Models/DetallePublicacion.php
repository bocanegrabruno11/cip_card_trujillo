<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePublicacion extends Model
{
    protected $table = 'detalle_publicaciones';

    protected $fillable = [
        'publicacion_id',
        'descripcion',
        'ruta_imagen', 
        'url_enlace', 
        'grupo' // Para saber si es la imagen 'principal' o del 'slider'
    ];

    public function publicacion()
    {
        return $this->belongsTo(Publicacion::class, 'publicacion_id');
    }
}