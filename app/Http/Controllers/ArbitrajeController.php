<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Arbitraje;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\Log;

class ArbitrajeController extends Controller
{
    public function registros()
    {
        return view('mesa-partes.arbitrajes.control');
    }

    // ─── Helper: determinar quién subió el documento ──────────────────────────
    private function determinarRolSubidor($documento, $arbitraje): array
    {
        if (!$documento->user) {
            return ['label' => 'Sistema', 'color' => 'secondary', 'icono' => 'fa-robot'];
        }

        $userPersonaDni = optional($documento->user->persona)->dni;

        // ¿El DNI del uploader coincide con alguna persona del arbitraje?
        if ($userPersonaDni) {
            $personaArbitraje = $arbitraje->personas->firstWhere('dni', $userPersonaDni);
            if ($personaArbitraje) {
                return $personaArbitraje->tipo === 'Demandante'
                    ? ['label' => 'Demandante', 'color' => 'success',  'icono' => 'fa-user-check']
                    : ['label' => 'Demandado',  'color' => 'warning',  'icono' => 'fa-user-shield'];
            }
        }

        // No coincide → fue subido por el administrador / gestor
        return ['label' => 'Administrador', 'color' => 'danger', 'icono' => 'fa-user-tie'];
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function obtenerArbitrajes()
    {
        try {
            $user   = Auth::user();
            $userId = $user->id;

            $persona = $user->persona;
            $userDni = $persona ? $persona->dni : null;

            Log::info('Buscando arbitrajes para:', ['user_id' => $userId, 'dni' => $userDni]);

            // 1. Arbitrajes creados por el usuario
            $arbitrajesCreados = Arbitraje::with([
                'personas',
                'procesos' => fn($q) => $q->orderBy('fecha_creacion', 'desc'),
                'procesos.documentos' => fn($q) => $q->orderBy('fecha_subida', 'desc'),
                'procesos.documentos.user.persona', // ← para subido_por
                'procesos.etapa',
            ])->where('user_id', $userId)->get();

            Log::info('Arbitrajes creados: ' . $arbitrajesCreados->count());

            // 2. Arbitrajes donde el usuario es demandante/demandado (por DNI)
            $arbitrajesPorDni = collect();
            if ($userDni) {
                $arbitrajesPorDni = Arbitraje::with([
                    'personas',
                    'procesos' => fn($q) => $q->orderBy('fecha_creacion', 'desc'),
                    'procesos.documentos' => fn($q) => $q->orderBy('fecha_subida', 'desc'),
                    'procesos.documentos.user.persona', // ← para subido_por
                    'procesos.etapa',
                ])
                ->whereHas('personas', fn($q) => $q->where('dni', $userDni))
                ->where('user_id', '!=', $userId)
                ->get();

                Log::info('Arbitrajes por DNI: ' . $arbitrajesPorDni->count());
            }

            // 3. Combinar y deduplicar
            $arbitrajes = $arbitrajesCreados->merge($arbitrajesPorDni)
                ->unique('id_arbitraje')
                ->sortByDesc('fecha_inicio')
                ->values();

            // 4. Formatear con subido_por en cada documento
            $arbitrajesFormateados = $arbitrajes->map(function ($arbitraje) use ($userId, $userDni) {
                $esCreador     = $arbitraje->user_id === $userId;
                $rolEnProceso  = null;

                if (!$esCreador && $userDni) {
                    $personaEncontrada = $arbitraje->personas->firstWhere('dni', $userDni);
                    if ($personaEncontrada) $rolEnProceso = $personaEncontrada->tipo;
                }

                $procesosFormateados = $arbitraje->procesos->map(function ($proceso) use ($arbitraje) {
                    return [
                        'id_proceso_de_arbitraje' => $proceso->id_proceso_de_arbitraje,
                        'fecha_creacion'           => $proceso->fecha_creacion,
                        'fecha_finalizacion'       => $proceso->fecha_finalizacion,
                        'estado'                   => $proceso->estado,
                        'controversia'             => $arbitraje->controversia,
                        'etapa'                    => $proceso->etapa ? [
                            'id'     => $proceso->etapa->id,
                            'nombre' => $proceso->etapa->nombre,
                        ] : null,
                        'documentos' => $proceso->documentos->map(function ($doc) use ($arbitraje) {
                            $rol = $this->determinarRolSubidor($doc, $arbitraje);
                            return [
                                'id_proceso_arbitraje_documento' => $doc->id_proceso_arbitraje_documento,
                                'tipo_documento'  => $doc->tipo_documento,
                                'nombre_original' => $doc->nombre_original,
                                'ruta_archivo'    => $doc->ruta_archivo,
                                'observaciones'   => $doc->observaciones,
                                'fecha_subida'    => $doc->fecha_subida,
                                'subido_por'      => [
                                    'nombre' => optional($doc->user)->name ?? 'N/A',
                                    'label'  => $rol['label'],
                                    'color'  => $rol['color'],
                                    'icono'  => $rol['icono'],
                                ],
                            ];
                        }),
                    ];
                });

                $personasFormateadas = $arbitraje->personas->map(fn($p) => [
                    'id_proceso_arbitraje_persona' => $p->id_proceso_arbitraje_persona,
                    'dni'       => $p->dni,
                    'nombres'   => $p->nombres,
                    'apellidos' => $p->apellidos,
                    'correo'    => $p->correo,
                    'telefono'  => $p->telefono,
                    'ruc'       => $p->ruc,
                    'tipo'      => $p->tipo,
                ]);

                return [
                    'id_arbitraje'         => $arbitraje->id_arbitraje,
                    'nombre_materia'        => $arbitraje->nombre_materia,
                    'pretenciones'          => $arbitraje->pretenciones,
                    'cuantia'               => $arbitraje->cuantia,
                    'controversia'          => $arbitraje->controversia,
                    'fundamentos_hecho'          => $arbitraje->fundamentos_hecho,
                    'tasa_solicitud'        => $arbitraje->tasa_solicitud,
                    'designacion_arbitral'  => $arbitraje->designacion_arbitral,
                    'fecha_inicio'          => $arbitraje->fecha_inicio,
                    'fecha_finalizacion'    => $arbitraje->fecha_finalizacion,
                    'estado'                => $arbitraje->estado,
                    'es_creador'            => $esCreador,
                    'rol_usuario'           => $esCreador ? 'Creador' : ($rolEnProceso ?? 'Observador'),
                    'personas'              => $personasFormateadas,
                    'procesos'              => $procesosFormateados,
                ];
            });

            return response()->json([
                'success'    => true,
                'arbitrajes' => $arbitrajesFormateados,
                'info'       => [
                    'creados_por_usuario' => $arbitrajesCreados->count(),
                    'como_parte'          => $arbitrajesPorDni->count(),
                    'total'               => $arbitrajes->count(),
                    'dni_usuario'         => $userDni,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener arbitrajes:', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los arbitrajes',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function archivar(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $arbitraje = Arbitraje::findOrFail($id);
            if (in_array($arbitraje->estado, ['archivado', 'terminado'])) {
                return response()->json(['success' => false, 'message' => 'El arbitraje ya está ' . $arbitraje->estado], 400);
            }
            $arbitraje->update(['estado' => 'archivado', 'fecha_finalizacion' => now()]);
            DB::commit();
            NotificacionService::notificarInvolucrados(
                $arbitraje, 
                'arbitraje', 
                'Expediente Archivado', 
                'El proceso de arbitraje ha sido archivado por la administración. No se realizarán más acciones sobre este expediente.'
            );
            return response()->json(['success' => true, 'message' => 'El arbitraje ha sido archivado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error en archivar:', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }
}