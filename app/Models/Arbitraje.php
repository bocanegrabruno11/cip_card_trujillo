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
        'descripcion',
        'estado'
    ];
    
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime',
    ];
    
    public $timestamps = false;
    
    // Relaciones
    public function procesos()
    {
        return $this->hasMany(ProcesoArbitraje::class, 'arbitraje_id', 'id_arbitraje');
    }
    
    public function personas()
    {
        return $this->hasMany(ProcesoArbitrajePersona::class, 'arbitraje_id', 'id_arbitraje');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}