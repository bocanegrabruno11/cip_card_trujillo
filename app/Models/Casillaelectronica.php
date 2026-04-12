<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasillaElectronica extends Model
{
    protected $table = 'casilla_electronica';
    protected $primaryKey = 'id_casilla';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'emisor_id', 'arbitraje_id', 'jrd_id', 'asunto', 'comentario', 'estado', 'fecha_registro'
    ];

    public function emisor() {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function arbitraje() {
        return $this->belongsTo(Arbitraje::class, 'arbitraje_id');
    }

    public function jrd() {
        return $this->belongsTo(Jrd::class, 'jrd_id');
    }
}