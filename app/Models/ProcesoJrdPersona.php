<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoJrdPersona extends Model
{
    protected $table = 'procesos_jrd_personas';
    protected $primaryKey = 'id_proceso_jrd_persona';
    public $timestamps = false;
    
    protected $fillable = [
        'jrd_id',
        'dni',
        'tipo',
        'nombres',
        'apellidos',
        'correo',
        'telefono',
        'ruc',
        'direccion'
    ];

    public function jrd()
    {
        return $this->belongsTo(Jrd::class, 'jrd_id', 'id_jrd');
    }
}