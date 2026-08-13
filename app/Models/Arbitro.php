<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arbitro extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellidos',
        'dni',
        'ruc',
        'telefono',
        'correo',
        'direccion'
    ];

    // Relación directa con la tabla pivote
    public function arbitroUsers()
    {
        return $this->hasMany(ArbitroUser::class);
    }

    // Relación con users a través de la pivote
    // SIN usar el modelo pivote explícitamente
    public function users()
    {
        return $this->belongsToMany(User::class, 'arbitro_user')
                    ->withTimestamps();
        // ELIMINAR: ->using(ArbitroUser::class);
    }

    // Método para obtener el nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellidos;
    }

    // Método para obtener usuarios activos
    public function getUsuariosActivos()
    {
        return $this->users()->where('activo', 1)->get();
    }
}