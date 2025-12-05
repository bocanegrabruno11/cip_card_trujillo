<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifaConfiguracion extends Model
{
    use HasFactory;

    protected $table = 'tarifas_configuracion';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
        'activo'
    ];
}