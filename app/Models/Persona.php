<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';
    protected $primaryKey = 'id_persona';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'dni',
        'correo_contacto',
        'direccion',
        'celular',
        'user_id'
    ];

    // Relación con Users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
