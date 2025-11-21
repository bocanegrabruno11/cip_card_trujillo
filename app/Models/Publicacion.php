<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publicacion extends Model
{
    protected $table = 'publicaciones';
    
    protected $fillable = ['titulo', 'descripcion', 'seccion', 'activo'];

    // Relación: Una publicación tiene muchos detalles (imágenes)
    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePublicacion::class, 'publicacion_id');
    }
}
