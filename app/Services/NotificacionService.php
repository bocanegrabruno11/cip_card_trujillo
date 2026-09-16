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

    public static function sincronizarCasillaRetroactiva($user, $dni)
    {
        // 1. Sincronizar Arbitrajes
        $arbitrajesPersonas = \App\Models\ProcesoArbitrajePersona::where('dni', $dni)->get();
        foreach ($arbitrajesPersonas as $ap) {
            $arbitraje = \App\Models\Arbitraje::find($ap->arbitraje_id);
            if ($arbitraje) {
                $existe = CasillaElectronica::where('user_id', $user->id)
                    ->where('arbitraje_id', $arbitraje->id_arbitraje)
                    ->where('asunto', 'like', 'Vinculación a Expediente%')
                    ->exists();

                if (!$existe) {
                    CasillaElectronica::insert([
                        'user_id'        => $user->id,
                        'emisor_id'      => $arbitraje->user_id, // Usamos al creador del arbitraje como emisor
                        'arbitraje_id'   => $arbitraje->id_arbitraje,
                        'jrd_id'         => null,
                        'asunto'         => 'Vinculación a Expediente - ' . $arbitraje->numero_expediente,
                        'comentario'     => "Usted ha sido vinculado como {$ap->tipo} al expediente N° {$arbitraje->numero_expediente}. Ingrese a Mis Arbitrajes para ver los detalles y documentos.",
                        'estado'         => 'no leido',
                        'fecha_registro' => now()
                    ]);
                }
            }
        }

        // 2. Sincronizar JRD
        $jrdPersonas = \App\Models\ProcesoJrdPersona::where('dni', $dni)->get();
        foreach ($jrdPersonas as $jp) {
            $jrd = \App\Models\Jrd::find($jp->jrd_id);
            if ($jrd) {
                $existe = CasillaElectronica::where('user_id', $user->id)
                    ->where('jrd_id', $jrd->id_jrd)
                    ->where('asunto', 'like', 'Vinculación a Expediente JRD%')
                    ->exists();

                if (!$existe) {
                    CasillaElectronica::insert([
                        'user_id'        => $user->id,
                        'emisor_id'      => $jrd->user_id, // Usamos al creador del jrd como emisor
                        'arbitraje_id'   => null,
                        'jrd_id'         => $jrd->id_jrd,
                        'asunto'         => 'Vinculación a Expediente JRD - ' . $jrd->numero_expediente,
                        'comentario'     => "Usted ha sido vinculado como {$jp->tipo} al expediente JRD N° {$jrd->numero_expediente}. Ingrese a Mis JRD para ver los detalles y documentos.",
                        'estado'         => 'no leido',
                        'fecha_registro' => now()
                    ]);
                }
            }
        }
    }
}