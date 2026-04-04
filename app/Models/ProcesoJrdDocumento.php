<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoJrdDocumento extends Model
{
    protected $table = 'procesos_jrd_documentos';
    protected $primaryKey = 'id_proceso_jrd_documento';
    public $timestamps = false;

    protected $fillable = [
        'proceso_jrd_id',
        'fecha_subida',
        'tipo_documento',
        'nombre_original',
        'ruta_archivo',
        'observaciones',
        'user_id'
    ];

    protected $casts = [
        'fecha_subida' => 'datetime'
    ];

    public function proceso()
    {
        return $this->belongsTo(ProcesoJrd::class, 'proceso_jrd_id', 'id_proceso_jrd');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}