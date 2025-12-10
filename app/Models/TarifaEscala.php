<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaEscala extends Model
{
    use HasFactory;

    protected $table = 'tarifas_escalas';

    protected $fillable = [
        'tipo',
        'tipo_calculadora',
        'rango_letra',
        'monto_min',
        'monto_max',
        'monto_fijo',
        'porcentaje_exceso',
        'base_exceso',
        'activo'
    ];

    // Helper para obtener el nombre legible del tipo
    public function getTipoLegibleAttribute()
    {
        return match($this->tipo) {
            'arbitro_unico' => 'Honorarios Árbitro Único',
            'tribunal_arbitral' => 'Honorarios Tribunal Arbitral',
            'gastos_administrativos' => 'Gastos Administrativos',
            default => $this->tipo,
        };
    }
}