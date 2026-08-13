<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'activo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con Persona (uno a uno)
     */
    public function persona()
    {
        return $this->hasOne(Persona::class, 'user_id', 'id');
    }

    /**
     * Relación directa con la tabla pivote arbitro_user
     */
    public function arbitroUsers()
    {
        return $this->hasMany(ArbitroUser::class);
    }

    /**
     * Relación con árbitros (muchos a muchos)
     * SIN usar el modelo pivote explícitamente
     */
    public function arbitros()
    {
        return $this->belongsToMany(Arbitro::class, 'arbitro_user')
                    ->withTimestamps();
        // ELIMINAR: ->using(ArbitroUser::class);
    }

    /**
     * Verificar si el usuario está activo
     */
    public function isActivo()
    {
        return $this->activo == 1;
    }

    /**
     * Obtener solo árbitros activos del usuario
     */
    public function getArbitrosActivos()
    {
        return $this->arbitros()->whereHas('users', function($query) {
            $query->where('activo', 1);
        })->get();
    }

    /**
     * Verificar si el usuario tiene un árbitro específico
     */
    public function hasArbitro($arbitroId)
    {
        return $this->arbitros()->where('arbitro_id', $arbitroId)->exists();
    }

    /**
     * Obtener el número de árbitros asignados al usuario
     */
    public function getCantidadArbitrosAttribute()
    {
        return $this->arbitros()->count();
    }

    /**
     * Scope para obtener solo usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Scope para obtener solo usuarios inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', 0);
    }
}