<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoJrd extends Model
{
    // ✅ Cambia 'proceso_jrd' por el nombre real de tu tabla
    protected $table = 'procesos_jrd';
    protected $primaryKey = 'id_proceso_jrd';
    public $timestamps = false;

    protected $fillable = [
        'jrd_id',
        'fecha_creacion',
        'fecha_finalizacion',
        'id_etapa_jrd',
        'estado',
    ];

    protected $casts = [
        'fecha_creacion'     => 'datetime',
        'fecha_finalizacion' => 'datetime',
    ];

    public function jrd()
    {
        return $this->belongsTo(Jrd::class, 'jrd_id', 'id_jrd');
    }

    public function etapa()
    {
        return $this->belongsTo(EtapaJrd::class, 'id_etapa_jrd');
    }

    public function documentos()
    {
        return $this->hasMany(ProcesoJrdDocumento::class, 'proceso_jrd_id', 'id_proceso_jrd');
    }
}