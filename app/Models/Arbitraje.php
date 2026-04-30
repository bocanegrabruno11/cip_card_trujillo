<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arbitraje extends Model
{
    protected $table = 'arbitraje'; 
    protected $primaryKey = 'id_arbitraje';
    
    protected $fillable = [
        'user_id',
        'fecha_inicio',
        'fecha_finalizacion',
        'nombre_materia',
        'pretenciones', // 👈 corregido (antes tenías 'descripcion')
        'cuantia',
        'designacion_arbitral',
        'tasa_solicitud',
        'estado',
        'tipo_arbitraje',
        'controversia',
        'fundamentos_hecho'
    ];
    
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime',
    ];
    
    public $timestamps = false;
    
    // 🔥 RELACIONES CORREGIDAS

// DESPUÉS
    public function procesos()
    {
        return $this->hasMany(ProcesoDeArbitraje::class, 'id_arbitraje', 'id_arbitraje')
                    ->orderBy('fecha_creacion', 'desc'); // 👈 Procesos más recientes primero
    }
    
    // 2. Personas del arbitraje
    public function personas()
    {
        return $this->hasMany(ProcesoArbitrajePersona::class, 'arbitraje_id', 'id_arbitraje');
    }
    
    // 3. Usuario dueño del arbitraje
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}