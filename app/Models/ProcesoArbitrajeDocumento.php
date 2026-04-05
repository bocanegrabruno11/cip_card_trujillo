<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoArbitrajeDocumento extends Model
{
    protected $table = 'procesos_arbitraje_documentos';
    protected $primaryKey = 'id_proceso_arbitraje_documento';
    
    protected $fillable = [
        'fecha_subida',
        'tipo_documento',
        'nombre_original',
        'ruta_archivo',
        'id_proceso_de_arbitraje', // 👈 FK correcta
        'observaciones',           // 👈 nuevo campo
        'user_id'                  // 👈 nuevo campo
    ];
    
    protected $casts = [
        'fecha_subida' => 'datetime',
    ];
    
    public $timestamps = false;
    
    // 🔥 Relación con proceso
    public function proceso()
    {
        return $this->belongsTo(
            ProcesoDeArbitraje::class,
            'id_proceso_de_arbitraje',
            'id_proceso_de_arbitraje'
        );
    }

    // 🔥 Relación con usuario
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }
    // En ProcesoArbitrajeDocumento.php

public function scopeVouchers($query)
{
    return $query->where('tipo_documento', 'voucher');
}

public function scopePendientes($query)
{
    return $query->where('observaciones', 'not like', '%[ACEPTADO]%')
                 ->where('observaciones', 'not like', '%[RECHAZADO]%');
}
}