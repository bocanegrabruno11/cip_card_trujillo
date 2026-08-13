<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot; // <--- IMPORTANTE: Extender de Pivot
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArbitroUser extends Pivot // <--- Cambiar de Model a Pivot
{
    use HasFactory;

    protected $table = 'arbitro_user';

    protected $fillable = [
        'arbitro_id',
        'user_id'
    ];

    // Relación con Arbitro
    public function arbitro()
    {
        return $this->belongsTo(Arbitro::class);
    }

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope para buscar por arbitro
    public function scopeByArbitro($query, $arbitroId)
    {
        return $query->where('arbitro_id', $arbitroId);
    }

    // Scope para buscar por user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Método para verificar si existe la relación
    public static function existsRelation($arbitroId, $userId)
    {
        return self::where('arbitro_id', $arbitroId)
                   ->where('user_id', $userId)
                   ->exists();
    }
}