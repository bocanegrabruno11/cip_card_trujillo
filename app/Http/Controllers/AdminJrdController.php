<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Jrd;
use App\Models\ProcesoJrd;
use App\Models\ProcesoJrdPersona;
use App\Models\User;
use App\Models\Persona;

class AdminJrdController extends Controller
{
    // Vista principal con listado de JRD
    public function index()
    {
        return view('Admin.Jrd');
    }
    
    // Obtener todos los JRD (para AJAX)
    public function obtenerJrd(Request $request)
    {
        try {
            $query = Jrd::with(['user', 'personas']);
            
            // Filtro por DNI si se proporciona
            if ($request->has('dni') && !empty($request->dni)) {
                $dni = $request->dni;
                
                $query->where(function($q) use ($dni) {
                    // Buscar por DNI en personas relacionadas
                    $q->whereHas('personas', function($subQ) use ($dni) {
                        $subQ->where('dni', 'LIKE', "%{$dni}%");
                    })
                    // O buscar por DNI del creador
                    ->orWhereHas('user.persona', function($subQ) use ($dni) {
                        $subQ->where('dni', 'LIKE', "%{$dni}%");
                    });
                });
            }
            
            $jrdList = $query->orderBy('fecha_inicio', 'desc')->get();
            
            // Agregar información del DNI del creador
            $jrdList = $jrdList->map(function($jrd) {
                $creador = $jrd->user;
                $personaCreador = $creador ? $creador->persona : null;
                
                $jrd->creador_nombre = $creador ? $creador->name : 'N/A';
                $jrd->creador_dni = $personaCreador ? $personaCreador->dni : 'N/A';
                
                return $jrd;
            });
            
            return response()->json([
                'success' => true,
                'jrd' => $jrdList,
                'total' => $jrdList->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener JRD (Admin):', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los JRD',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Vista de detalle de un JRD específico
    public function detalle($id)
    {
        try {
            $jrd = Jrd::with([
                'procesos' => function($query) {
                    $query->orderBy('fecha', 'desc');
                },
                'procesos.documentos',
                'personas',
                'user',
                'user.persona'
            ])->findOrFail($id);
            
            return view('Admin.jrd-detalle', compact('jrd'));
            
        } catch (\Exception $e) {
            Log::error('Error al obtener detalle de JRD:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.jrd.index')
                ->with('error', 'JRD no encontrado');
        }
    }

  public function aceptar(Request $request, $id_jrd)
    {
        try {
            $request->validate([
                'comentario' => 'nullable|string|max:500'
            ]);

            $jrd = Jrd::find($id_jrd);
            
            if (!$jrd) {
                return response()->json([
                    'success' => false,
                    'message' => 'JRD no encontrado.'
                ], 404);
            }

            // Obtener el proceso actual (validación de voucher)
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

            // Verificar que sea el proceso de validación de voucher
            if (!str_contains(strtolower($procesoActual->nombre), 'validacion') || 
                !(str_contains(strtolower($procesoActual->nombre), 'voucher') || 
                  str_contains(strtolower($procesoActual->nombre), 'pago'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este no es un proceso de validación de voucher/pago.'
                ], 400);
            }

            // Finalizar proceso actual
            $procesoActual->estado = 'Finalizado';
            $procesoActual->save();

            // Cambiar estado del JRD
            $jrd->estado = 'en proceso';
            $jrd->save();

            // Crear siguiente proceso (Reunion de asignacion de adjudicadores)
            $nuevoProceso = ProcesoJrd::create([
                'jrd_id'      => $id_jrd,
                'fecha'       => now(),
                'nombre'      => 'Reunion de asignacion de adjudicadores',
                'descripcion' => 'Reunión para asignar los adjudicadores responsables del caso.',
                'estado'      => 'activo'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Voucher aprobado correctamente. Se ha creado el siguiente proceso: Reunion de asignacion de adjudicadores.',
                'data' => [
                    'jrd_estado' => $jrd->estado,
                    'nuevo_proceso' => $nuevoProceso->nombre,
                    'comentario' => $request->comentario
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al aceptar el voucher: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechazar JRD completo
     */
    public function rechazar(Request $request, $id_jrd)
    {
        try {
            $request->validate([
                'motivo' => 'required|string|max:500'
            ]);

            $jrd = Jrd::find($id_jrd);
            
            if (!$jrd) {
                return response()->json([
                    'success' => false,
                    'message' => 'JRD no encontrado.'
                ], 404);
            }

            // Rechazar JRD
            $jrd->estado = 'rechazado';
            $jrd->fecha_finalizacion = now();
            $jrd->save();

            // Finalizar todos los procesos activos
            ProcesoJrd::where('jrd_id', $id_jrd)
                ->where('estado', '!=', 'Finalizado')
                ->update(['estado' => 'Finalizado']);

            return response()->json([
                'success' => true,
                'message' => 'JRD rechazado correctamente.',
                'data' => [
                    'jrd_estado' => $jrd->estado,
                    'motivo' => $request->motivo
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar el JRD: ' . $e->getMessage()
            ], 500);
        }
    }

    // Obtener un JRD específico (para AJAX)
    public function obtenerUno($id)
    {
        try {
            $jrd = Jrd::with([
                'user.persona',
                'personas',
                'procesos' => function($query) {
                    $query->orderBy('fecha', 'desc');
                },
                'procesos.documentos'
            ])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'jrd' => $jrd
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener JRD individual:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'JRD no encontrado'
            ], 404);
        }
    }
}