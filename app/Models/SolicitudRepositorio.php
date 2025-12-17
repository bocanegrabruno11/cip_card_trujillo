<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SolicitudRepositorio extends Model
{
    use HasFactory;

    // Nombre de la tabla
    protected $table = 'solicitudes_repositorio';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'nombres',
        'email',
        'dni',
        'foto_dni_path',
        'estado',         // 'pendiente', 'aprobado', 'rechazado'
        'fecha_respuesta'
    ];

    /**
     * Accesor para obtener la URL pública de la foto del DNI.
     * Uso en vista: $solicitud->foto_url
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto_dni_path) {
            return Storage::url($this->foto_dni_path);
        }
        return null; // O una imagen por defecto
    }

    /**
     * Relación "virtual" para ver si este correo coincide con un usuario registrado.
     * Esto te permite en la vista hacer: $solicitud->usuarioRegistrado
     * Si devuelve null, es que el correo de la solicitud no existe en tu sistema.
     */
    public function usuarioRegistrado()
    {
        // Asumiendo que tu tabla de usuarios es 'users' y el modelo es 'User'
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Scope para filtrar pendientes fácilmente.
     * Uso: SolicitudRepositorio::pendientes()->get();
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}