<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jrd extends Model
{
    protected $table = 'jrd';
    protected $primaryKey = 'id_jrd';
        public $timestamps = false;

    protected $fillable = [
        'user_id',
        'fecha_inicio',
        'fecha_finalizacion',
        'nombre_materia',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_finalizacion' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function procesos()
    {
        return $this->hasMany(ProcesoJrd::class, 'jrd_id', 'id_jrd');
    }

    public function personas()
    {
        return $this->hasMany(ProcesoJrdPersona::class, 'jrd_id', 'id_jrd');
    }
}