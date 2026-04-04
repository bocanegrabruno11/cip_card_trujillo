<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jrd extends Model
{
    protected $table = 'jrd';
    protected $primaryKey = 'id_jrd';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'fecha_inicio',
        'fecha_finalizacion',
        'nombre_materia',
        'pretenciones',
        'cuantia',
        'designacion_adjudicadores',
        'tasa_solicitud',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime',
        // ❌ ELIMINADOS: 'cuantia' y 'tasa_solicitud' como decimal:2
        // causaban el error 500 al recibir texto desde el formulario
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function procesos()
    {
        return $this->hasMany(ProcesoJrd::class, 'jrd_id', 'id_jrd');
    }

    public function procesoActivo()
    {
        return $this->hasOne(ProcesoJrd::class, 'jrd_id', 'id_jrd')
            ->where('estado', 'activo')
            ->orderBy('fecha_creacion', 'desc');
    }

public function procesoActivoConEtapa()
{
    return $this->hasOne(ProcesoJrd::class, 'jrd_id', 'id_jrd')
        ->where('estado', 'activo')
        ->orderBy('fecha_creacion', 'desc');
}

    public function personas()
    {
        return $this->hasMany(ProcesoJrdPersona::class, 'jrd_id', 'id_jrd');
    }
}