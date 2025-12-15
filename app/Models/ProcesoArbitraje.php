<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoArbitraje extends Model
{
    protected $table = 'procesos_arbitraje';
    protected $primaryKey = 'id_proceso_arbitraje';
    
    protected $fillable = [
        'arbitraje_id',
        'fecha',
        'nombre',
        'descripcion',
        'estado'
    ];
    
    protected $casts = [
        'fecha' => 'datetime',
    ];
    
    public $timestamps = false;
    
    public function arbitraje()
    {
        return $this->belongsTo(Arbitraje::class, 'arbitraje_id', 'id_arbitraje');
    }
    
    public function documentos()
    {
        return $this->hasMany(ProcesoArbitrajeDocumento::class, 'proceso_arbitraje_id', 'id_proceso_arbitraje');
    }
}