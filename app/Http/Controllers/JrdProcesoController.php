<?php

namespace App\Http\Controllers;

use App\Models\ProcesoJrd;
use App\Models\Jrd;
use Illuminate\Http\Request;

class JrdProcesoController extends Controller
{
    public function pasarSiguienteProceso(Request $request, $id_jrd)
    {
        try {
            // 0️⃣ Validar JRD
            $jrd = Jrd::find($id_jrd);

            if (!$jrd) {
                return response()->json([
                    'success' => false,
                    'message' => 'JRD no encontrado.'
                ], 404);
            }

            if ($jrd->estado === 'terminado') {
                return response()->json([
                    'success' => false,
                    'message' => 'El JRD ya se encuentra finalizado.'
                ], 400);
            }

            // 1️⃣ Obtener proceso activo
            $procesoActual = ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('estado', '!=', 'Finalizado')
                ->orderBy('fecha', 'desc')
                ->first();

            if (!$procesoActual) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay proceso activo.'
                ], 404);
            }

            // 2️⃣ Finalizar proceso actual
            $procesoActual->estado = 'Finalizado';
            $procesoActual->save();

            // 3️⃣ Flujo de procesos - NUEVO FLUJO
            $nuevoProcesoNombre = '';
            $nuevaDescripcion = '';
            
            // PRIMER PROCESO: Validacion de Voucher
            if (str_contains(strtolower($procesoActual->nombre), 'validacion') && 
                (str_contains(strtolower($procesoActual->nombre), 'voucher') || 
                 str_contains(strtolower($procesoActual->nombre), 'pago'))) {
                
                // 👉 JRD en proceso
                $jrd->estado = 'en proceso';
                $jrd->save();

                $nuevoProcesoNombre = 'Reunion de asignacion de adjudicadores';
                $nuevaDescripcion = 'Reunión para asignar los adjudicadores responsables del caso.';
                
                $message = 'Voucher validado correctamente. Se creó: Reunion de asignacion de adjudicadores';

            } 
            // SEGUNDO PROCESO: Reunion de asignacion de adjudicadores
            elseif (str_contains(strtolower($procesoActual->nombre), 'reunion') && 
                   (str_contains(strtolower($procesoActual->nombre), 'asignacion') ||
                    str_contains(strtolower($procesoActual->nombre), 'adjudicadores'))) {
                
                // 👉 JRD en proceso
                $jrd->estado = 'en proceso';
                $jrd->save();

                $nuevoProcesoNombre = 'Contrato tripartito';
                $nuevaDescripcion = 'Elaboración y firma del contrato tripartito entre las partes.';
                
                $message = 'Proceso actualizado correctamente. Se creó: Contrato tripartito';

            } 
            // TERCER PROCESO: Contrato tripartito
            elseif (str_contains(strtolower($procesoActual->nombre), 'contrato') && 
                   str_contains(strtolower($procesoActual->nombre), 'tripartito')) {
                
                // 👉 JRD en proceso
                $jrd->estado = 'en proceso';
                $jrd->save();

                $nuevoProcesoNombre = 'Pago';
                $nuevaDescripcion = 'Proceso de pago y liquidación final.';
                
                $message = 'Proceso actualizado correctamente. Se creó: Pago';

            } 
            // CUARTO PROCESO: Pago
            elseif (str_contains(strtolower($procesoActual->nombre), 'pago')) {
                
                // 👉 Finalizar JRD
                $jrd->estado = 'terminado';
                $jrd->fecha_finalizacion = now();
                $jrd->save();

                $message = 'JRD finalizado correctamente. Proceso de pago completado.';
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'jrd_estado' => $jrd->estado,
                        'finalizado' => true
                    ]
                ]);

            } else {
                // Si el proceso no coincide con ninguno de los flujos predefinidos
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudo determinar el siguiente proceso. Nombre del proceso actual: ' . $procesoActual->nombre,
                    'debug' => [
                        'nombre_proceso_actual' => $procesoActual->nombre,
                        'lowercase_nombre' => strtolower($procesoActual->nombre)
                    ]
                ], 400);
            }

            // Crear el nuevo proceso
            $nuevoProceso = ProcesoJrd::create([
                'jrd_id'      => $id_jrd,
                'fecha'       => now(),
                'nombre'      => $nuevoProcesoNombre,
                'descripcion' => $nuevaDescripcion,
                'estado'      => 'activo'
            ]);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'proceso_actual_finalizado' => $procesoActual->nombre,
                    'nuevo_proceso' => $nuevoProcesoNombre,
                    'jrd_estado' => $jrd->estado
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método para crear un proceso personalizado
     */
    public function crearProceso(Request $request, $id_jrd)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'estado' => 'required|string|max:150'
            ]);

            $jrd = Jrd::find($id_jrd);
            
            if (!$jrd) {
                return back()->with('error', 'JRD no encontrado.');
            }

            // Finalizar el proceso actual si existe
            $procesoActual = ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('estado', '!=', 'Finalizado')
                ->orderBy('fecha', 'desc')
                ->first();

            if ($procesoActual) {
                $procesoActual->estado = 'Finalizado';
                $procesoActual->save();
            }

            // Crear nuevo proceso
            ProcesoJrd::create([
                'jrd_id'      => $id_jrd,
                'fecha'       => now(),
                'nombre'      => $request->nombre,
                'descripcion' => $request->descripcion,
                'estado'      => $request->estado
            ]);

            return back()->with('success', 'Proceso personalizado creado correctamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el proceso: ' . $e->getMessage());
        }
    }

    /**
     * Método para actualizar estado de un proceso específico
     */
    public function actualizarEstadoProceso(Request $request, $id_jrd, $id_proceso)
    {
        try {
            $request->validate([
                'estado' => 'required|string|max:150'
            ]);

            $proceso = ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('id_proceso_jrd', $id_proceso)
                ->first();

            if (!$proceso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proceso no encontrado.'
                ], 404);
            }

            $proceso->estado = $request->estado;
            $proceso->save();

            return response()->json([
                'success' => true,
                'message' => 'Estado del proceso actualizado correctamente.',
                'data' => [
                    'proceso_id' => $proceso->id_proceso_jrd,
                    'estado' => $proceso->estado
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método para obtener el proceso activo actual
     */
    public function obtenerProcesoActivo($id_jrd)
    {
        try {
            $procesoActual = ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('estado', '!=', 'Finalizado')
                ->orderBy('fecha', 'desc')
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
     * Método para obtener todos los procesos de un JRD
     */
    public function obtenerProcesos($id_jrd)
    {
        try {
            $procesos = ProcesoJrd::where('jrd_id', $id_jrd)
                ->orderBy('fecha', 'asc')
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