<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtapaJrd extends Model
{
    protected $table = 'etapas_jrd';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nombre',
        'estado'
    ];
    
    public $timestamps = false;
    
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }
    
    // Relación con procesos
    public function procesos()
    {
        return $this->hasMany(ProcesoJrd::class, 'id_etapa_jrd', 'id');
    }
}