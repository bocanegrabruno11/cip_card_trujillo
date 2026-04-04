<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoArbitrajePersona extends Model
{
    protected $table = 'procesos_arbitraje_personas';
    protected $primaryKey = 'id_proceso_arbitraje_persona';
    
    protected $fillable = [
        'arbitraje_id',
        'dni',
        'nombres',
        'apellidos',
        'correo',
        'telefono',
        'ruc',
        'tipo'
    ];
    
    public $timestamps = false;
    
    // 🔥 Relación con arbitraje
    public function arbitraje()
    {
        return $this->belongsTo(
            Arbitraje::class,
            'arbitraje_id',
            'id_arbitraje'
        );
    }
}