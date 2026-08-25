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

    // =============================================
    // RELACIONES Y FUNCIONES PARA ÁRBITROS
    // =============================================

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
    }

    /**
     * Verifica si el usuario está registrado como árbitro
     */
    public function esArbitro()
    {
        return $this->arbitros()->exists();
    }

    /**
     * Verificar si el usuario tiene un árbitro específico
     */
    public function hasArbitro($arbitroId)
    {
        return $this->arbitros()->where('arbitro_id', $arbitroId)->exists();
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
     * Obtener el número de árbitros asignados al usuario
     */
    public function getCantidadArbitrosAttribute()
    {
        return $this->arbitros()->count();
    }

    // =============================================
    // RELACIONES Y FUNCIONES PARA ADJUDICADORES
    // =============================================

    /**
     * Relación directa con la tabla pivote adjudicador_user
     */
    public function adjudicadorUsers()
    {
        return $this->hasMany(AdjudicadorUser::class);
    }

    /**
     * Relación con adjudicadores (muchos a muchos)
     */
    public function adjudicadores()
    {
        return $this->belongsToMany(Adjudicador::class, 'adjudicador_user')
                    ->withTimestamps();
    }

    /**
     * Verifica si el usuario está registrado como adjudicador
     */
    public function esAdjudicador()
    {
        return $this->adjudicadores()->exists();
    }

    /**
     * Verificar si el usuario tiene un adjudicador específico
     */
    public function hasAdjudicador($adjudicadorId)
    {
        return $this->adjudicadores()->where('adjudicador_id', $adjudicadorId)->exists();
    }

    /**
     * Obtener solo adjudicadores activos del usuario
     */
    public function getAdjudicadoresActivos()
    {
        return $this->adjudicadores()->whereHas('users', function($query) {
            $query->where('activo', 1);
        })->get();
    }

    /**
     * Obtener el número de adjudicadores asignados al usuario
     */
    public function getCantidadAdjudicadoresAttribute()
    {
        return $this->adjudicadores()->count();
    }

    // =============================================
    // FUNCIONES PARA ADMINISTRADOR
    // =============================================

    /**
     * Verifica si el usuario tiene el rol de administrador
     */
    public function esAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Verifica si el usuario es EXCLUSIVAMENTE administrador
     * (NO es árbitro ni adjudicador)
     */
    public function esAdminPuro()
    {
        return $this->hasRole('admin') && !$this->esArbitro() && !$this->esAdjudicador();
    }

    // =============================================
    // FUNCIONES GENERALES PARA EL USUARIO
    // =============================================

    /**
     * Verificar si el usuario está activo
     */
    public function isActivo()
    {
        return $this->activo == 1;
    }

    /**
     * Obtiene el rol principal del usuario
     * Prioridad: admin > adjudicador > arbitro
     */
    public function getRolPrincipal()
    {
        if ($this->esAdminPuro()) {
            return 'Administrador';
        } elseif ($this->esAdjudicador()) {
            return 'Adjudicador';
        } elseif ($this->esArbitro()) {
            return 'Árbitro';
        }
        return 'Usuario';
    }

    /**
     * Obtiene el color de la badge según el rol
     */
    public function getRolColor()
    {
        if ($this->esAdminPuro()) {
            return 'danger'; // Rojo
        } elseif ($this->esAdjudicador()) {
            return 'info'; // Azul
        } elseif ($this->esArbitro()) {
            return 'info'; // Azul
        }
        return 'secondary';
    }

    /**
     * Obtiene el ícono según el rol
     */
    public function getRolIcono()
    {
        if ($this->esAdminPuro()) {
            return '🔴';
        } elseif ($this->esAdjudicador()) {
            return '🔵';
        } elseif ($this->esArbitro()) {
            return '🔵';
        }
        return '⚪';
    }

    // =============================================
    // SCOPES (Filtros)
    // =============================================

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