<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Jrd;
use App\Models\ProcesoJrd;
use App\Models\ProcesoJrdPersona;
use App\Models\ProcesoJrdDocumento;
use App\Models\EtapaJrd;
use App\Models\User;
use App\Services\NotificacionService;

class AdminJrdController extends Controller
{
    public function index()
    {
        return view('Admin.Jrd');
    }

    public function obtenerJrd(Request $request)
    {
        try {
            $query = Jrd::with([
                'user.persona',
                'personas',
                'procesos' => function($q) {
                    $q->orderBy('fecha_creacion', 'asc')->with('etapa');
                },
                'procesos.documentos' => function($q) {
                    $q->orderBy('fecha_subida', 'desc');
                },
                'procesos.documentos.user.persona'
            ]);

            if ($request->filled('dni')) {
                $dni = $request->dni;
                $query->where(function($q) use ($dni) {
                    $q->whereHas('personas', function($subQ) use ($dni) {
                        $subQ->where('dni', 'LIKE', "%{$dni}%");
                    })->orWhereHas('user.persona', function($subQ) use ($dni) {
                        $subQ->where('dni', 'LIKE', "%{$dni}%");
                    });
                });
            }

            $jrdList = $query->orderBy('fecha_inicio', 'desc')->get();

            $formattedJrd = $jrdList->map(function($jrd) {
                $creador        = $jrd->user;
                $personaCreador = $creador ? $creador->persona : null;

                // Proceso activo → etapa actual
                $procesoActivo = $jrd->procesos->firstWhere('estado', 'activo');
                $etapaActual   = ($procesoActivo && $procesoActivo->etapa)
                    ? $procesoActivo->etapa->nombre
                    : 'Sin etapa';

                return [
                    'id_jrd'                     => $jrd->id_jrd,
                    'nombre_materia'             => $jrd->nombre_materia,
                    'pretenciones'               => $jrd->pretenciones,
                    'cuantia'                    => $jrd->cuantia,
                    'tasa_solicitud'             => $jrd->tasa_solicitud,
                    'designacion_adjudicadores'  => $jrd->designacion_adjudicadores,
                    'fecha_inicio'               => $jrd->fecha_inicio,
                    'fecha_finalizacion'         => $jrd->fecha_finalizacion,
                    'estado'                     => $jrd->estado,
                    'creador_nombre'             => $creador ? $creador->name : 'Usuario #' . $jrd->user_id,
                    'creador_dni'                => $personaCreador ? $personaCreador->dni : 'N/A',
                    'etapa_actual'               => $etapaActual,
                    'personas' => $jrd->personas->map(fn($p) => [
                        'id_proceso_jrd_persona' => $p->id_proceso_jrd_persona,
                        'dni'       => $p->dni,
                        'nombres'   => $p->nombres,
                        'apellidos' => $p->apellidos,
                        'correo'    => $p->correo,
                        'telefono'  => $p->telefono,
                        'ruc'       => $p->ruc,
                        'tipo'      => $p->tipo,
                    ]),
                    'procesos' => $jrd->procesos->map(function($proceso) use ($jrd) {
                        return [
                            'id_proceso_jrd'     => $proceso->id_proceso_jrd,
                            'fecha_creacion'     => $proceso->fecha_creacion,
                            'fecha_finalizacion' => $proceso->fecha_finalizacion,
                            'estado'             => $proceso->estado,
                            'etapa'              => $proceso->etapa ? [
                                'id'     => $proceso->etapa->id,
                                'nombre' => $proceso->etapa->nombre,
                            ] : null,
                            'documentos' => $proceso->documentos->map(function($doc) use ($jrd) {
                                $uploaderDni  = optional(optional($doc->user)->persona)->dni;
                                $personaMatch = $uploaderDni
                                    ? $jrd->personas->firstWhere('dni', $uploaderDni)
                                    : null;

                                if ($personaMatch) {
                                    $rolLabel = $personaMatch->tipo;
                                    $rolColor = $personaMatch->tipo === 'Solicitante' ? 'success' : 'warning';
                                    $rolIcono = $personaMatch->tipo === 'Solicitante' ? 'fa-user-check' : 'fa-user-shield';
                                } else {
                                    $rolLabel = 'Administrador';
                                    $rolColor = 'danger';
                                    $rolIcono = 'fa-user-tie';
                                }

                                return [
                                    'id_proceso_jrd_documento' => $doc->id_proceso_jrd_documento,
                                    'tipo_documento'  => $doc->tipo_documento,
                                    'nombre_original' => $doc->nombre_original,
                                    'ruta_archivo'    => $doc->ruta_archivo,
                                    'observaciones'   => $doc->observaciones,
                                    'fecha_subida'    => $doc->fecha_subida,
                                    'subido_por'      => [
                                        'nombre' => optional($doc->user)->name ?? 'N/A',
                                        'label'  => $rolLabel,
                                        'color'  => $rolColor,
                                        'icono'  => $rolIcono,
                                    ],
                                ];
                            }),
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'jrd'     => $formattedJrd,
                'total'   => $formattedJrd->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener JRD (Admin):', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los JRD: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function detalle($id)
    {
        try {
            $jrd = Jrd::with([
                'procesos' => function($query) {
                    $query->orderBy('fecha_creacion', 'asc')->with('etapa');
                },
                'procesos.documentos.user',
                'personas',
                'user.persona',
                'procesoActivoConEtapa'
            ])->findOrFail($id);

            $etapasActivas = EtapaJrd::activos()->get();

            return view('Admin.Jrd-detalle', compact('jrd', 'etapasActivas'));

        } catch (\Exception $e) {
            Log::error('Error al obtener detalle de JRD:', [
                'id'      => $id,
                'message' => $e->getMessage()
            ]);

            return redirect()->route('admin.jrd.index')
                ->with('error', 'JRD no encontrado');
        }
    }

    public function aceptarVoucher(Request $request, $id_jrd)
    {
        try {
            $request->validate([
                'comentario' => 'nullable|string|max:500'
            ]);

            $jrd = Jrd::findOrFail($id_jrd);

            $procesoActual = ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('estado', 'activo')
                ->with('etapa')
                ->first();

            if (!$procesoActual) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay proceso activo.'
                ], 404);
            }

            $tieneVoucher = ProcesoJrdDocumento::where('proceso_jrd_id', $procesoActual->id_proceso_jrd)
                ->where('tipo_documento', 'voucher')
                ->exists();

            if (!$tieneVoucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este proceso no tiene voucher para validar.'
                ], 400);
            }

            $procesoActual->estado             = 'finalizado';
            $procesoActual->fecha_finalizacion = now();
            $procesoActual->save();

            NotificacionService::notificarTitular(
                $jrd, 
                'jrd', 
                'Voucher Validado', 
                'Su comprobante de pago ha sido aprobado exitosamente por la administración.'
            );
            $siguienteEtapa = EtapaJrd::where('estado', 1)
                ->where('id', '>', $procesoActual->id_etapa_jrd)
                ->orderBy('id', 'asc')
                ->first();

            if ($siguienteEtapa) {
                ProcesoJrd::create([
                    'jrd_id'             => $id_jrd,
                    'fecha_creacion'     => now(),
                    'fecha_finalizacion' => null,
                    'id_etapa_jrd'       => $siguienteEtapa->id,
                    'estado'             => 'activo'
                ]);

                $jrd->estado = 'en proceso';
                $jrd->save();
                NotificacionService::notificarInvolucrados(
                    $jrd, 
                    'jrd', 
                    'Avance de Etapa en JRD', 
                    "El expediente JRD ha avanzado a la etapa: {$siguienteEtapa->nombre}."
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Voucher aprobado. Ahora en etapa: ' . $siguienteEtapa->nombre,
                ]);
            }

            $jrd->estado             = 'terminado';
            $jrd->fecha_finalizacion = now();
            $jrd->save();
            NotificacionService::notificarInvolucrados(
                $jrd, 
                'jrd', 
                'Proceso JRD Finalizado', 
                'El expediente correspondiente a esta Junta de Resolución de Disputas ha sido concluido formalmente.'
            );

            return response()->json([
                'success' => true,
                'message' => 'Voucher aprobado y JRD finalizado (última etapa completada).',
            ]);

        } catch (\Exception $e) {
            Log::error('Error al aceptar voucher:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al aceptar el voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function rechazarVoucher(Request $request, $id_jrd)
    {
        try {
            $request->validate([
                'motivo' => 'required|string|max:500'
            ]);

            $jrd = Jrd::findOrFail($id_jrd);
            $jrd->estado = 'observado';
            $jrd->save();

            ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('estado', 'activo')
                ->update(['estado' => 'observado']);

                NotificacionService::notificarTitular(
                $jrd, 
                'jrd', 
                'Solicitud Observada - Voucher Rechazado', 
                "El voucher subido ha sido observado por la administración. Motivo: {$request->motivo}."
            );
            return response()->json([
                'success' => true,
                'message' => 'Voucher rechazado. JRD marcado como observado.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error al rechazar voucher:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar el voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    public function archivar(Request $request, $id_jrd)
    {
        try {
            $jrd = Jrd::findOrFail($id_jrd);
            $jrd->estado             = 'archivado';
            $jrd->fecha_finalizacion = now();
            $jrd->save();

            ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('estado', 'activo')
                ->update([
                    'estado'             => 'finalizado',
                    'fecha_finalizacion' => now()
                ]);

                NotificacionService::notificarInvolucrados(
                    $jrd, 
                    'jrd', 
                    'Expediente JRD Archivado', 
                    'El proceso correspondiente a esta Junta de Resolución de Disputas ha sido archivado por la administración.'
                );

            return response()->json([
                'success' => true,
                'message' => 'JRD archivado correctamente.',
            ]);

        } catch (\Exception $e) {
            Log::error('Error al archivar JRD:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al archivar el JRD: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenerUno($id)
    {
        try {
            $jrd = Jrd::with([
                'user.persona',
                'personas',
                'procesos' => function($query) {
                    $query->orderBy('fecha_creacion', 'desc')->with('etapa', 'documentos');
                },
                'procesoActivoConEtapa'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'jrd'     => $jrd
            ]);

        } catch (\Exception $e) {
            Log::error('Error al obtener JRD individual:', [
                'id'      => $id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'JRD no encontrado'
            ], 404);
        }
    }
}