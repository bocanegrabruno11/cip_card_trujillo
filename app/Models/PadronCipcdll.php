<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PadronCipcdll extends Model
{
    protected $table = 'padron_cipcdll';

    protected $primaryKey = 'id';

    public $timestamps = false; // porque tu tabla no tiene created_at ni updated_at

    protected $fillable = [
        'cip',
        'nombres',
        'apellidos',
        'capitulo',
        'dni'
    ];
}