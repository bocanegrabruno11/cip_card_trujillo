<?php

namespace App\Http\Controllers;

use App\Models\ProcesoDeArbitraje;
use App\Models\Arbitraje;
use App\Models\EtapaArbitral;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcesoDeArbitrajeController extends Controller
{
    /**
     * Listar procesos de un arbitraje
     */
    public function index($id_arbitraje)
    {
        try {
            $procesos = ProcesoDeArbitraje::with(['etapa', 'documentos'])
                ->where('id_arbitraje', $id_arbitraje)
                ->orderBy('fecha_creacion', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'procesos' => $procesos
            ]);
        } catch (\Exception $e) {
            Log::error('Error al listar procesos:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los procesos'
            ], 500);
        }
    }

    /**
     * Mostrar un proceso específico
     */
    public function show($id_proceso)
    {
        try {
            $proceso = ProcesoDeArbitraje::with(['etapa', 'documentos', 'arbitraje'])
                ->findOrFail($id_proceso);
            
            return response()->json([
                'success' => true,
                'proceso' => $proceso
            ]);
        } catch (\Exception $e) {
            Log::error('Error al mostrar proceso:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Proceso no encontrado'
            ], 404);
        }
    }

    /**
     * Finalizar un proceso y crear el siguiente (si existe)
     */
    public function pasarSiguiente($id_proceso)
    {
        try {
            DB::beginTransaction();
            
            // 1. Obtener proceso actual con su arbitraje
            $procesoActual = ProcesoDeArbitraje::with('arbitraje')->findOrFail($id_proceso);
            $arbitraje = $procesoActual->arbitraje;
            
            // Verificar que el proceso esté iniciado
            if ($procesoActual->estado !== 'iniciado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este proceso ya fue finalizado'
                ], 400);
            }
            
            // 2. Finalizar proceso actual
            $procesoActual->update([
                'estado' => 'finalizado',
                'fecha_finalizacion' => now()
            ]);
            
            // 3. Buscar siguiente etapa activa
            $siguienteEtapa = EtapaArbitral::where('id', '>', $procesoActual->id_etapa_arbitral)
                ->where('estado', 1)
                ->orderBy('id', 'asc')
                ->first();
            
            $arbitrajeTerminado = false;
            
            if ($siguienteEtapa) {
                // 4. Crear nuevo proceso con la siguiente etapa
                $nuevoProceso = ProcesoDeArbitraje::create([
                    'fecha_creacion' => now(),
                    'id_etapa_arbitral' => $siguienteEtapa->id,
                    'id_arbitraje' => $procesoActual->id_arbitraje,
                    'estado' => 'iniciado'
                ]);
                
                // Actualizar estado del arbitraje a "en proceso"
                $arbitraje->update([
                    'estado' => 'en proceso'
                ]);
                
                $mensaje = "Proceso '{$procesoActual->etapa->nombre}' finalizado. Se ha creado el siguiente proceso: '{$siguienteEtapa->nombre}'";
                NotificacionService::notificarInvolucrados(
                    $arbitraje, 
                    'arbitraje', 
                    'Avance de Etapa', 
                    "El expediente ha avanzado a la etapa: '{$siguienteEtapa->nombre}'."
                );
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'hay_siguiente' => true,
                    'proceso_anterior' => $procesoActual,
                    'nuevo_proceso' => $nuevoProceso
                ]);
            } else {
                // No hay más etapas - arbitraje terminado
                $arbitraje->update([
                    'estado' => 'terminado',
                    'fecha_finalizacion' => now()
                ]);
                
                $mensaje = "Proceso '{$procesoActual->etapa->nombre}' finalizado. ¡El arbitraje ha sido completado exitosamente!";
                NotificacionService::notificarInvolucrados(
                    $arbitraje, 
                    'arbitraje', 
                    'Arbitraje Concluido', 
                    "Se han completado todas las etapas procesales. El arbitraje ha finalizado exitosamente."
                );
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'hay_siguiente' => false,
                    'arbitraje_terminado' => true,
                    'proceso_finalizado' => $procesoActual
                ]);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al pasar al siguiente proceso:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear el primer proceso para un arbitraje (cuando se acepta el voucher)
     */
    public function crearPrimerProceso($id_arbitraje)
    {
        try {
            $arbitraje = Arbitraje::findOrFail($id_arbitraje);
            
            // Verificar que no tenga procesos
            $procesosExistentes = ProcesoDeArbitraje::where('id_arbitraje', $id_arbitraje)->count();
            if ($procesosExistentes > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El arbitraje ya tiene procesos creados'
                ], 400);
            }
            
            // Buscar la primera etapa activa
            $primeraEtapa = EtapaArbitral::where('estado', 1)
                ->orderBy('id', 'asc')
                ->first();
            
            if (!$primeraEtapa) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay etapas activas configuradas'
                ], 400);
            }
            
            // Crear el primer proceso
            $proceso = ProcesoDeArbitraje::create([
                'fecha_creacion' => now(),
                'id_etapa_arbitral' => $primeraEtapa->id,
                'id_arbitraje' => $id_arbitraje,
                'estado' => 'iniciado'
            ]);
            
            // Actualizar estado del arbitraje
            $arbitraje->update([
                'estado' => 'iniciado'
            ]);
            NotificacionService::notificarTitular(
                $arbitraje, 
                'arbitraje', 
                'Inicio de Proceso Arbitral', 
                "Se ha dado inicio formal a su proceso de arbitraje en la etapa: '{$primeraEtapa->nombre}'."
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Proceso creado exitosamente',
                'proceso' => $proceso
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al crear primer proceso:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener el proceso activo actual de un arbitraje
     */
    public function obtenerProcesoActivo($id_arbitraje)
    {
        try {
            $procesoActivo = ProcesoDeArbitraje::with(['etapa', 'documentos'])
                ->where('id_arbitraje', $id_arbitraje)
                ->where('estado', 'iniciado')
                ->first();
            
            if (!$procesoActivo) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay procesos activos para este arbitraje'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'proceso_activo' => $procesoActivo
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener proceso activo:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar el proceso activo'
            ], 500);
        }
    }

    /**
     * Obtener todos los procesos con sus etapas
     */
    public function obtenerConEtapas($id_arbitraje)
    {
        try {
            $procesos = ProcesoDeArbitraje::with(['etapa', 'documentos.user'])
                ->where('id_arbitraje', $id_arbitraje)
                ->orderBy('fecha_creacion', 'desc')
                ->get()
                ->map(function($proceso) {
                    return [
                        'id' => $proceso->id_proceso_de_arbitraje,
                        'etapa_nombre' => $proceso->etapa ? $proceso->etapa->nombre : 'Sin etapa',
                        'etapa_id' => $proceso->id_etapa_arbitral,
                        'fecha_creacion' => $proceso->fecha_creacion,
                        'fecha_finalizacion' => $proceso->fecha_finalizacion,
                        'estado' => $proceso->estado,
                        'documentos_count' => $proceso->documentos->count(),
                        'documentos' => $proceso->documentos->map(function($doc) {
                            return [
                                'id' => $doc->id_proceso_arbitraje_documento,
                                'nombre' => $doc->nombre_original,
                                'tipo' => $doc->tipo_documento,
                                'fecha' => $doc->fecha_subida,
                                'usuario' => $doc->user ? $doc->user->name : null
                            ];
                        })
                    ];
                });
            
            return response()->json([
                'success' => true,
                'procesos' => $procesos
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener procesos con etapas:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los procesos'
            ], 500);
        }
    }
}