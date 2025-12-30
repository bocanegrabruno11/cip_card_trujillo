<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcesoJrd extends Model
{
    protected $table = 'procesos_jrd';
    protected $primaryKey = 'id_proceso_jrd';
        public $timestamps = false;

    protected $fillable = [
        'jrd_id',
        'fecha',
        'nombre',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    public function jrd()
    {
        return $this->belongsTo(Jrd::class, 'jrd_id', 'id_jrd');
    }

    public function documentos()
    {
        return $this->hasMany(ProcesoJrdDocumento::class, 'proceso_jrd_id');
    }
}