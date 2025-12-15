<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoArbitrajeDocumento extends Model
{
    protected $table = 'procesos_arbitraje_documentos';
    protected $primaryKey = 'id_proceso_arbitraje_documento';
    
    protected $fillable = [
        'proceso_arbitraje_id',
        'fecha_subida',
        'tipo_documento',
        'nombre_original',
        'ruta_archivo'
    ];
    
    protected $casts = [
        'fecha_subida' => 'datetime',
    ];
    
    public $timestamps = false;
    
    public function proceso()
    {
        return $this->belongsTo(ProcesoArbitraje::class, 'proceso_arbitraje_id', 'id_proceso_arbitraje');
    }
}