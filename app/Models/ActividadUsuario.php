<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActividadUsuario extends Model
{
    protected $table = 'actividad_usuarios';
    
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'view_accessed'
    ];

    /**
     * Registra una acción de auditoría.
     *
     * @param string $action Descripción de la acción realizada (ej: "Creó un arbitraje")
     * @param string|null $view Nombre de la vista o módulo afectado (ej: "Mesa de Partes - Arbitrajes")
     * @return void
     */
    public static function log($action, $view = null)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Obtener el nombre del rol (asumiendo Spatie/Permission)
            $roles = $user->roles->pluck('name')->toArray();
            $roleName = !empty($roles) ? implode(', ', $roles) : 'Sin rol';

            self::create([
                'user_id' => $user->id,
                'user_name' => $user->name ?? 'Usuario Desconocido',
                'user_role' => $roleName,
                'action' => $action,
                'view_accessed' => $view
            ]);
        }
    }
}
