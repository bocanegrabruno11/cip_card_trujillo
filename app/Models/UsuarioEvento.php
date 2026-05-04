<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioEvento extends Model
{
    protected $table = 'usuarios_eventos_cipcdll';

    protected $fillable = ['usuario', 'password'];

    public $timestamps = false;
}