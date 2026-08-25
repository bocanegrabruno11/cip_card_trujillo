<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjudicador extends Model
{
    use HasFactory;

    protected $table = 'adjudicadores';

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
    public function adjudicadorUsers()
    {
        return $this->hasMany(AdjudicadorUser::class);
    }

    // Relación con users a través de la pivote
    // SIN usar el modelo pivote explícitamente
    public function users()
    {
        return $this->belongsToMany(User::class, 'adjudicador_user')
                    ->withTimestamps();
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