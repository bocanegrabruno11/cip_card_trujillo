<?php

namespace App\Http\Controllers;

use App\Models\ProcesoJrd;
use App\Models\Jrd;
use App\Models\EtapaJrd;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JrdProcesoController extends Controller
{
    /**
     * Pasar al siguiente proceso (creado por admin al aprobar)
     * Este método ahora es llamado SOLO desde el admin al aceptar el voucher
     */
/**
 * Pasar a la siguiente etapa (sin validación de voucher)
 */
public function pasarSiguienteProceso(Request $request, $id_jrd)
{
    try {
        $jrd = Jrd::find($id_jrd);

        if (!$jrd) {
            return response()->json([
                'success' => false,
                'message' => 'JRD no encontrado.'
            ], 404);
        }

        if ($jrd->estado === 'terminado' || $jrd->estado === 'archivado') {
            return response()->json([
                'success' => false,
                'message' => 'El JRD ya está finalizado o archivado.'
            ], 400);
        }

        // Obtener proceso activo
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

        // Finalizar proceso actual
        $procesoActual->estado = 'finalizado';
        $procesoActual->fecha_finalizacion = now();
        $procesoActual->save();

        // Buscar siguiente etapa activa
        $siguienteEtapa = EtapaJrd::where('estado', 1)
            ->where('id', '>', $procesoActual->id_etapa_jrd)
            ->orderBy('id', 'asc')
            ->first();

        if ($siguienteEtapa) {
            $nuevoProceso = ProcesoJrd::create([
                'jrd_id'           => $id_jrd,
                'fecha_creacion'   => now(),
                'fecha_finalizacion' => null,
                'id_etapa_jrd'     => $siguienteEtapa->id,
                'estado'           => 'activo'
            ]);
            
            $jrd->estado = 'en proceso';
            $jrd->save();

            NotificacionService::notificarInvolucrados(
                    $jrd, 
                    'jrd', 
                    'Avance de Etapa en JRD', 
                    "El expediente JRD ha avanzado de la etapa '" . ($procesoActual->etapa->nombre ?? 'Anterior') . "' a la etapa: '{$siguienteEtapa->nombre}'."
                );
            return response()->json([
                'success' => true,
                'message' => 'Proceso avanzado a: ' . $siguienteEtapa->nombre,
                'data' => [
                    'etapa_anterior' => $procesoActual->etapa->nombre ?? 'Anterior',
                    'etapa_actual' => $siguienteEtapa->nombre,
                    'jrd_estado' => $jrd->estado
                ]
            ]);
        } else {
            // Última etapa completada
            $jrd->estado = 'terminado';
            $jrd->fecha_finalizacion = now();
            $jrd->save();

            return response()->json([
                'success' => true,
                'message' => 'Proceso finalizado correctamente. JRD terminado.',
                'data' => [
                    'finalizado' => true,
                    'jrd_estado' => $jrd->estado
                ]
            ]);
        }

    } catch (\Exception $e) {
        Log::error('Error al pasar siguiente proceso:', ['error' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Obtener el proceso activo actual
     */
    public function obtenerProcesoActivo($id_jrd)
    {
        try {
            $procesoActual = ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('estado', 'activo')
                ->with('etapa', 'documentos')
                ->first();

            if (!$procesoActual) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay proceso activo'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'proceso' => $procesoActual
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el proceso activo'
            ], 500);
        }
    }

    /**
     * Obtener todos los procesos de un JRD
     */
    public function obtenerProcesos($id_jrd)
    {
        try {
            $procesos = ProcesoJrd::where('jrd_id', $id_jrd)
                ->with('etapa', 'documentos')
                ->orderBy('fecha_creacion', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'procesos' => $procesos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los procesos'
            ], 500);
        }
    }
}