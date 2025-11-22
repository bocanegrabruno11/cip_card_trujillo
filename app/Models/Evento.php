<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_evento',
        'lugar',
        'activo'
    ];

    // Relación con las imágenes
    public function detalles()
    {
        return $this->hasMany(DetalleEvento::class, 'evento_id');
    }
}