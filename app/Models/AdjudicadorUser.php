<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdjudicadorUser extends Pivot
{
    use HasFactory;

    protected $table = 'adjudicador_user';

    protected $fillable = [
        'adjudicador_id',
        'user_id'
    ];

    // Relación con Adjudicador
    public function adjudicador()
    {
        return $this->belongsTo(Adjudicador::class);
    }

    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope para buscar por adjudicador
    public function scopeByAdjudicador($query, $adjudicadorId)
    {
        return $query->where('adjudicador_id', $adjudicadorId);
    }

    // Scope para buscar por user
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Método para verificar si existe la relación
    public static function existsRelation($adjudicadorId, $userId)
    {
        return self::where('adjudicador_id', $adjudicadorId)
                   ->where('user_id', $userId)
                   ->exists();
    }
}
