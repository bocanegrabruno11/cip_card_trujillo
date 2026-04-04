<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EtapaArbitral extends Model
{
    protected $table = 'etapas_arbitrales';
    protected $primaryKey = 'id'; // 👈 Agrega esto

    protected $fillable = [
        'nombre',
        'estado'
    ];

    public $timestamps = false; // porque ya manejas manualmente las fechas

    // Scope para solo activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }
}