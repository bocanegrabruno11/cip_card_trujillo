<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Arbitraje;
use App\Models\User;
use App\Models\Persona;
use App\Models\ProcesoArbitrajePersona;
use App\Models\ProcesoDeArbitraje;
use App\Models\EtapaArbitral;
use App\Models\ProcesoArbitrajeDocumento;
use App\Services\NotificacionService;

class AdminArbitrajeController extends Controller
{
    // ─── Helper: determinar quién subió el documento ──────────────────────────
    private function determinarRolSubidor($documento, $arbitraje): array
    {
        if (!$documento->user) {
            return ['label' => 'Sistema', 'color' => 'secondary', 'icono' => 'fa-robot'];
        }

        $userPersonaDni = optional($documento->user->persona)->dni;

        if ($userPersonaDni) {
            $personaArbitraje = $arbitraje->personas->firstWhere('dni', $userPersonaDni);
            if ($personaArbitraje) {
                return $personaArbitraje->tipo === 'Demandante'
                    ? ['label' => 'Demandante', 'color' => 'success',      'icono' => 'fa-user-check']
                    : ['label' => 'Demandado',  'color' => 'warning',      'icono' => 'fa-user-shield'];
            }
        }

        return ['label' => 'Administrador', 'color' => 'danger', 'icono' => 'fa-user-tie'];
    }

    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        return view('Admin.Arbitraje');
    }

    public function obtenerTodos(Request $request)
    {
        try {
            $query = Arbitraje::with([
                'personas',
                'procesos' => function ($query) {
                    $query->orderBy('fecha_creacion', 'desc');
                },
                'procesos.etapa',
                'procesos.documentos' => function ($query) {
                    $query->orderBy('fecha_subida', 'desc');
                },
                'procesos.documentos.user.persona',
                'user.persona'
            ]);

            if ($request->has('dni') && $request->dni) {
                $dni = $request->dni;
                $query->where(function ($q) use ($dni) {
                    $q->whereHas('user.persona', function ($sub) use ($dni) {
                        $sub->where('dni', $dni);
                    })->orWhereHas('personas', function ($sub) use ($dni) {
                        $sub->where('dni', $dni);
                    });
                });
            }

            $arbitrajes = $query->orderBy('fecha_inicio', 'desc')->get();

            $formattedArbitrajes = $arbitrajes->map(function ($arbitraje) {
                $creador        = $arbitraje->user;
                $personaCreador = $creador ? $creador->persona : null;

                // ✅ Título formateado para usar en la vista
                $tituloExpediente = $arbitraje->numero_expediente 
                    ? "Expediente N° {$arbitraje->numero_expediente}"
                    : ($arbitraje->nombre_materia ?? 'Sin expediente');

                return [
                    'id_arbitraje'         => $arbitraje->id_arbitraje,
                    'numero_expediente'    => $arbitraje->numero_expediente, // ✅ AGREGADO
                    'titulo_expediente'    => $tituloExpediente, // ✅ TÍTULO FORMATEADO
                    'nombre_materia'       => $arbitraje->nombre_materia,
                    'pretenciones'         => $arbitraje->pretenciones,
                    'cuantia'              => $arbitraje->cuantia,
                    'tasa_solicitud'       => $arbitraje->tasa_solicitud,
                    'designacion_arbitral' => $arbitraje->designacion_arbitral,
                    'fecha_inicio'         => $arbitraje->fecha_inicio,
                    'fecha_finalizacion'   => $arbitraje->fecha_finalizacion,
                    'estado'               => $arbitraje->estado,
                    'controversia'         => $arbitraje->controversia,
                    'fundamentos_hecho'    => $arbitraje->fundamentos_hecho,
                    'tipo_arbitraje'       => $arbitraje->tipo_arbitraje ?? 'normal',
                    'creador_nombre'       => $creador ? $creador->name : 'Usuario #' . $arbitraje->user_id,
                    'creador_dni'          => $personaCreador ? $personaCreador->dni : 'N/A',
                    'personas'             => $arbitraje->personas->map(fn($p) => [
                        'id_proceso_arbitraje_persona' => $p->id_proceso_arbitraje_persona,
                        'dni'       => $p->dni,
                        'nombres'   => $p->nombres,
                        'apellidos' => $p->apellidos,
                        'correo'    => $p->correo,
                        'telefono'  => $p->telefono,
                        'ruc'       => $p->ruc,
                        'tipo'      => $p->tipo,
                        'direccion' => $p->direccion,
                    ]),
                    'procesos' => $arbitraje->procesos->map(function ($proceso) use ($arbitraje) {
                        return [
                            'id_proceso_de_arbitraje' => $proceso->id_proceso_de_arbitraje,
                            'fecha_creacion'           => $proceso->fecha_creacion,
                            'fecha_finalizacion'       => $proceso->fecha_finalizacion,
                            'estado'                   => $proceso->estado,
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
                    }),
                ];
            });

            return response()->json([
                'success'    => true,
                'arbitrajes' => $formattedArbitrajes,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en obtenerTodos:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function detalle($id)
    {
        try {
            $arbitraje = Arbitraje::with([
                'personas',
                'procesos.etapa',
                'procesos.documentos' => function ($query) {
                    $query->orderBy('fecha_subida', 'desc');
                },
                'procesos.documentos.user.persona',
                'user.persona',
            ])->findOrFail($id);

            $procesosOrdenados = $arbitraje->procesos->sortByDesc('fecha_creacion');
            $arbitraje->setRelation('procesos', $procesosOrdenados);

            return view('Admin.arbitraje-detalle', compact('arbitraje'));

        } catch (\Exception $e) {
            \Log::error('Error en detalle:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            dd('Error: ' . $e->getMessage(), $e->getFile(), $e->getLine());
        }
    }

    public function pasarSiguienteProceso(Request $request, $id_arbitraje)
    {
        try {
            DB::beginTransaction();

            $arbitraje      = Arbitraje::findOrFail($id_arbitraje);
            $procesoActualId = $request->proceso_actual_id;

            if ($arbitraje->estado === 'terminado') {
                return response()->json(['success' => false, 'message' => 'El arbitraje ya está terminado'], 400);
            }

            $procesoActual = ProcesoDeArbitraje::where('id_proceso_de_arbitraje', $procesoActualId)
                ->where('id_arbitraje', $id_arbitraje)->first();

            if (!$procesoActual) {
                return response()->json(['success' => false, 'message' => 'Proceso no encontrado'], 404);
            }
            if ($procesoActual->estado !== 'iniciado') {
                return response()->json(['success' => false, 'message' => 'Este proceso ya fue finalizado'], 400);
            }

            $etapaActual       = $procesoActual->etapa;
            $nombreEtapaActual = $etapaActual ? $etapaActual->nombre : 'Proceso';

            $procesoActual->update(['estado' => 'finalizado', 'fecha_finalizacion' => now()]);

            $siguienteEtapa = EtapaArbitral::where('id', '>', $procesoActual->id_etapa_arbitral)
                ->where('estado', 1)->orderBy('id', 'asc')->first();

            if ($siguienteEtapa) {
                $nuevoProceso = ProcesoDeArbitraje::create([
                    'fecha_creacion'    => now(),
                    'id_etapa_arbitral' => $siguienteEtapa->id,
                    'id_arbitraje'      => $id_arbitraje,
                    'estado'            => 'iniciado',
                ]);
                if ($arbitraje->estado === 'iniciado') {
                    $arbitraje->update(['estado' => 'en proceso']);
                }
                NotificacionService::notificarInvolucrados(
                    $arbitraje, 
                    'arbitraje', 
                    'Avance de Etapa en Expediente', 
                    "El proceso de arbitraje ha avanzado a la etapa: {$siguienteEtapa->nombre}."
                );
                DB::commit();
                return response()->json([
                    'success'        => true,
                    'message'        => "Proceso '{$nombreEtapaActual}' finalizado. Se ha creado el siguiente: '{$siguienteEtapa->nombre}'",
                    'hay_siguiente'  => true,
                    'nuevo_proceso_id' => $nuevoProceso->id_proceso_de_arbitraje,
                    'siguiente_etapa'  => $siguienteEtapa->nombre,
                ]);
            } else {
                $arbitraje->update(['estado' => 'terminado', 'fecha_finalizacion' => now()]);
                NotificacionService::notificarInvolucrados(
                    $arbitraje, 
                    'arbitraje', 
                    'Proceso Finalizado', 
                    "El proceso de arbitraje correspondiente a este expediente ha sido concluido formalmente."
                );
                DB::commit();
                return response()->json([
                    'success'            => true,
                    'message'            => "Proceso '{$nombreEtapaActual}' finalizado. ¡Arbitraje completado!",
                    'hay_siguiente'      => false,
                    'arbitraje_terminado' => true,
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error en pasarSiguienteProceso:', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function aceptar($id)
    {
        try {
            DB::beginTransaction();
            $arbitraje = Arbitraje::findOrFail($id);
            if ($arbitraje->estado !== 'validando') {
                return response()->json(['success' => false, 'message' => 'El arbitraje no está en estado de validación'], 400);
            }
            $arbitraje->update(['estado' => 'iniciado']);
            $procesoExistente = ProcesoDeArbitraje::where('id_arbitraje', $id)->first();
            if (!$procesoExistente) {
                $primeraEtapa = EtapaArbitral::where('estado', 1)->orderBy('id', 'asc')->first();
                if ($primeraEtapa) {
                    ProcesoDeArbitraje::create([
                        'fecha_creacion'    => now(),
                        'id_etapa_arbitral' => $primeraEtapa->id,
                        'id_arbitraje'      => $id,
                        'estado'            => 'iniciado',
                    ]);
                }
            }
            NotificacionService::notificarTitular(
                $arbitraje, 
                'arbitraje', 
                'Solicitud de Arbitraje Aprobada', 
                'Su solicitud y voucher de pago han sido validados exitosamente. El proceso de arbitraje ha iniciado.'
            );
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Arbitraje aceptado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function rechazar(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $arbitraje = Arbitraje::findOrFail($id);
            $motivo    = $request->input('motivo', 'No se especificó motivo');
            if ($arbitraje->estado !== 'validando') {
                return response()->json(['success' => false, 'message' => 'El arbitraje no está en estado de validación'], 400);
            }
            $arbitraje->update(['estado' => 'observado']);
            $voucher = ProcesoArbitrajeDocumento::whereHas('proceso', function ($query) use ($id) {
                $query->where('id_arbitraje', $id);
            })->where('tipo_documento', 'voucher')->first();
            if ($voucher) {
                $voucher->update([
                    'observaciones' => ($voucher->observaciones ? $voucher->observaciones . "\n" : '')
                        . "[RECHAZADO] Motivo: {$motivo} - Fecha: " . now(),
                ]);
            }
            NotificacionService::notificarTitular(
                $arbitraje, 
                'arbitraje', 
                'Solicitud de Arbitraje Observada', 
                "Su solicitud de arbitraje ha sido observada. Motivo detallado: {$motivo}. Por favor, revise y actualice su información."
            );
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Arbitraje rechazado y marcado como observado']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

public function archivar(Request $request, $id)
{
    try {
        DB::beginTransaction();
        $arbitraje = Arbitraje::findOrFail($id);
        
        // Verificar si ya está archivado o terminado
        if (in_array($arbitraje->estado, ['archivado', 'terminado', 'finalizado'])) {
            return response()->json([
                'success' => false, 
                'message' => 'El arbitraje ya está ' . $arbitraje->estado
            ], 400);
        }
        
        // Solo archivar, NO pasar al siguiente proceso
        $arbitraje->update([
            'estado' => 'archivado', 
            'fecha_finalizacion' => now()
        ]);
        
        // Notificar a los involucrados
        NotificacionService::notificarInvolucrados(
            $arbitraje, 
            'arbitraje', 
            'Expediente Archivado', 
            'El proceso de arbitraje ha sido archivado por la administración. No se realizarán más acciones sobre este expediente.'
        );
        
        DB::commit();
        
        return response()->json([
            'success' => true, 
            'message' => 'El arbitraje ha sido archivado correctamente'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error en archivar:', ['message' => $e->getMessage()]);
        return response()->json([
            'success' => false, 
            'message' => 'Error interno: ' . $e->getMessage()
        ], 500);
    }
}
}