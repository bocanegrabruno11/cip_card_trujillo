<?php

namespace App\Services;

use App\Models\CasillaElectronica;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;

class NotificacionService
{
    public static function notificarInvolucrados($registro, $tipo, $asunto, $comentario)
    {
        // 1. Obtener DNIs de los involucrados según el tipo
        $dnis = $registro->personas()->pluck('dni')->toArray();

        // 2. Buscar los user_id reales en la tabla 'persona'
        $usuariosDestino = Persona::whereIn('dni', $dnis)
            ->whereNotNull('user_id')
            ->pluck('user_id')
            ->toArray();

        $notificaciones = [];
        $ahora = now();
        $emisorId = Auth::id();

        foreach ($usuariosDestino as $userId) {
            // No notificarse a sí mismo
            if ($userId == $emisorId) continue;

            $notificaciones[] = [
                'user_id'      => $userId,
                'emisor_id'    => $emisorId,
                'arbitraje_id' => ($tipo === 'arbitraje') ? $registro->id_arbitraje : null,
                'jrd_id'       => ($tipo === 'jrd') ? $registro->id_jrd : null,
                'asunto'       => $asunto,
                'comentario'   => $comentario,
                'estado'       => 'no leido',
                'fecha_registro' => $ahora
            ];
        }

        // 3. Inserción masiva (Bulk Insert)
        if (count($notificaciones) > 0) {
            CasillaElectronica::insert($notificaciones);
        }
    }

    public static function notificarTitular($registro, $tipo, $asunto, $comentario)
    {
        $userId = $registro->user_id; // El dueño del trámite
        $emisorId = Auth::id();

        // Evitar que el sistema le mande una notificación al mismo admin/usuario si él mismo hace la acción
        if ($userId == $emisorId) return;

        CasillaElectronica::insert([
            'user_id'        => $userId,
            'emisor_id'      => $emisorId,
            'arbitraje_id'   => ($tipo === 'arbitraje') ? $registro->id_arbitraje : null,
            'jrd_id'         => ($tipo === 'jrd') ? $registro->id_jrd : null,
            'asunto'         => $asunto,
            'comentario'     => $comentario,
            'estado'         => 'no leido',
            'fecha_registro' => now()
        ]);
    }
}