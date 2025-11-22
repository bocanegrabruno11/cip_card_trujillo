<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleEvento extends Model
{
    protected $table = 'detalle_eventos';

    protected $fillable = [
        'evento_id',
        'ruta_imagen',
        'tipo' // 'principal' o 'galeria'
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
}