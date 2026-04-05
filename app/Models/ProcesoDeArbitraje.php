<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoDeArbitraje extends Model
{
    protected $table = 'proceso_de_arbitraje';
    protected $primaryKey = 'id_proceso_de_arbitraje';
    
    protected $fillable = [
        'fecha_creacion',
        'fecha_finalizacion',
        'id_etapa_arbitral',
        'id_arbitraje',
        'estado'
    ];
    
    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_finalizacion' => 'datetime',
    ];
    
    public $timestamps = false;
    
    public function arbitraje()
    {
        return $this->belongsTo(Arbitraje::class, 'id_arbitraje', 'id_arbitraje');
    }
    
    public function etapa()
    {
        return $this->belongsTo(EtapaArbitral::class, 'id_etapa_arbitral', 'id');
    }
    
    // 👈 AGREGAR ESTA RELACIÓN
        public function documentos()
        {
            return $this->hasMany(ProcesoArbitrajeDocumento::class, 'id_proceso_de_arbitraje', 'id_proceso_de_arbitraje')
                        ->orderBy('fecha_subida', 'desc'); // 👈 Agregar esta línea
        }
}