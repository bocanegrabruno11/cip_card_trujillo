<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arbitraje;
use App\Models\ProcesoArbitraje;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AdminArbitrajeController extends Controller
{
    // Vista principal con listado de arbitrajes
    public function index()
    {
        return view('Admin.Arbitraje');
    }
    
    // Obtener todos los arbitrajes (para AJAX)
    public function obtenerTodos(Request $request)
    {
        try {
            $query = Arbitraje::with(['user', 'personas']);
            
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
            
            $arbitrajes = $query->orderBy('fecha_inicio', 'desc')->get();
            
            // Agregar información del DNI del creador
            $arbitrajes = $arbitrajes->map(function($arbitraje) {
                $creador = $arbitraje->user;
                $personaCreador = $creador ? $creador->persona : null;
                
                $arbitraje->creador_nombre = $creador ? $creador->name : 'N/A';
                $arbitraje->creador_dni = $personaCreador ? $personaCreador->dni : 'N/A';
                
                return $arbitraje;
            });
            
            return response()->json([
                'success' => true,
                'arbitrajes' => $arbitrajes,
                'total' => $arbitrajes->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener arbitrajes (Admin):', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los arbitrajes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // Vista de detalle de un arbitraje específico
    public function detalle($id)
    {
        try {
            $arbitraje = Arbitraje::with([
                'procesos' => function($query) {
                    $query->orderBy('fecha', 'desc');
                },
                'procesos.documentos',
                'personas',
                'user',
                'user.persona'
            ])->findOrFail($id);
            
            return view('Admin.arbitraje-detalle', compact('arbitraje'));
            
        } catch (\Exception $e) {
            Log::error('Error al obtener detalle de arbitraje:', [
                'id' => $id,
                'message' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.arbitrajes.index')
                ->with('error', 'Arbitraje no encontrado');
        }
    }

    // Rechazar arbitraje con motivo personalizado
 // Versión mejorada del método rechazar
public function rechazar(Request $request, $id_arbitraje)
{
    try {
        $request->validate([
            'motivo' => 'required|string|max:500'
        ], [
            'motivo.required' => 'Debe proporcionar un motivo para el rechazo',
            'motivo.max' => 'El motivo no puede exceder 500 caracteres'
        ]);
        
        DB::beginTransaction();
        
        // ============ DEPURACIÓN DETALLADA ============
        
        // 1. Verificar el arbitraje existe
        $arbitraje = Arbitraje::find($id_arbitraje);
        
        if (!$arbitraje) {
            return response()->json([
                'success' => false,
                'message' => 'Arbitraje no encontrado con ID: ' . $id_arbitraje
            ], 404);
        }
        
        // 2. DEPURACIÓN: Ver todos los procesos del arbitraje
        $todosProcesos = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)->get();
        
        // Mostrar información de depuración
        $procesosInfo = $todosProcesos->map(function($p) {
            return [
                'id' => $p->id_proceso_arbitraje,
                'nombre' => $p->nombre,
                'estado' => $p->estado,
                'descripcion' => substr($p->descripcion, 0, 50) . '...',
                'fecha' => $p->fecha->format('Y-m-d H:i:s')
            ];
        });
        
        Log::info('=== DEPURACIÓN PROCESOS ARBITRAJE ===', [
            'arbitraje_id' => $id_arbitraje,
            'total_procesos' => $todosProcesos->count(),
            'procesos' => $procesosInfo->toArray()
        ]);
        
        // 3. DEPURACIÓN: Verificar la consulta específica
        $sql = "SELECT * FROM procesos_arbitraje WHERE arbitraje_id = ? AND nombre = ? AND estado = ?";
        $resultadosRaw = DB::select($sql, [$id_arbitraje, 'Validacion de Voucher', 'Iniciado']);
        
        Log::info('=== CONSULTA SQL DIRECTA ===', [
            'sql' => $sql,
            'parametros' => [$id_arbitraje, 'Validacion de Voucher', 'Iniciado'],
            'resultados' => count($resultadosRaw),
            'primer_resultado' => $resultadosRaw ? (array)$resultadosRaw[0] : null
        ]);
        
        // 4. Buscar con Eloquent para comparar
        $procesoEloquent = ProcesoArbitraje::where([
            ['arbitraje_id', '=', $id_arbitraje],
            ['nombre', '=', 'Validacion de Voucher'],
            ['estado', '=', 'Iniciado']
        ])->get();
        
        Log::info('=== CONSULTA ELOQUENT ===', [
            'total_resultados_eloquent' => $procesoEloquent->count(),
            'resultados' => $procesoEloquent->map(function($p) {
                return $p->toArray();
            })->toArray()
        ]);
        
        // 5. Buscar sin condiciones estrictas para debug
        $procesosIniciados = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)
            ->where('estado', 'Iniciado')
            ->get();
            
        $procesosVoucher = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)
            ->where('nombre', 'like', '%voucher%')
            ->get();
        
        Log::info('=== OTRAS CONSULTAS ===', [
            'procesos_iniciados_count' => $procesosIniciados->count(),
            'procesos_iniciados' => $procesosIniciados->map(fn($p) => $p->nombre)->toArray(),
            'procesos_voucher_count' => $procesosVoucher->count(),
            'procesos_voucher' => $procesosVoucher->map(fn($p) => $p->nombre . ' (' . $p->estado . ')')->toArray()
        ]);
        
        // ============ LÓGICA PRINCIPAL ============
        
        // 6. Buscar el proceso de voucher
        $procesoVoucher = ProcesoArbitraje::where([
            ['arbitraje_id', '=', $id_arbitraje],
            ['nombre', '=', 'Validacion de Voucher'],
            ['estado', '=', 'Iniciado']
        ])->first();
        
        // Si no se encuentra, intentar con búsqueda más flexible
        if (!$procesoVoucher) {
            // Primero buscar cualquier proceso con "voucher" en el nombre
            $procesoVoucher = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)
                ->where('nombre', 'like', '%voucher%')
                ->whereIn('estado', ['Iniciado', 'En proceso'])
                ->first();
                
            // Si aún no, buscar cualquier proceso iniciado
            if (!$procesoVoucher) {
                $procesoVoucher = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)
                    ->where('estado', 'Iniciado')
                    ->first();
                    
                if (!$procesoVoucher) {
                    // Si no hay ningún proceso iniciado, buscar cualquier proceso
                    $procesoVoucher = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)
                        ->orderBy('fecha', 'desc')
                        ->first();
                        
                    if (!$procesoVoucher) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'No se encontró ningún proceso para este arbitraje',
                            'debug_info' => [
                                'arbitraje_id' => $id_arbitraje,
                                'todos_procesos' => $procesosInfo->toArray(),
                                'consulta_sql_resultados' => count($resultadosRaw),
                                'consulta_eloquent_resultados' => $procesoEloquent->count(),
                                'procesos_iniciados' => $procesosIniciados->map(fn($p) => $p->toArray())->toArray()
                            ]
                        ], 404);
                    }
                }
            }
        }
        
        Log::info('=== PROCESO ENCONTRADO PARA RECHAZAR ===', [
            'proceso_id' => $procesoVoucher->id_proceso_arbitraje,
            'nombre' => $procesoVoucher->nombre,
            'estado_original' => $procesoVoucher->estado,
            'estado_nuevo' => 'Rechazado'
        ]);
        
        // 7. Actualizar el proceso encontrado
        $procesoVoucher->estado = 'Rechazado';
        if (strpos($procesoVoucher->descripcion, '[RECHAZADO]') === false) {
            $procesoVoucher->descripcion .= " [RECHAZADO - Motivo: " . $request->motivo . "]";
        }
        $procesoVoucher->save();
        
        // 8. Crear nuevo proceso de registro del rechazo
        $nuevoProceso = ProcesoArbitraje::create([
            'arbitraje_id' => $id_arbitraje,
            'fecha' => now(),
            'nombre' => 'Arbitraje Rechazado',
            'descripcion' => "El arbitraje completo ha sido rechazado. Motivo: " . $request->motivo,
            'estado' => 'Finalizado'
        ]);
        
        // 9. Actualizar el arbitraje
        $arbitraje->estado = 'rechazado';
        $arbitraje->fecha_finalizacion = now();
        $arbitraje->save();
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Arbitraje rechazado exitosamente',
            'data' => [
                'proceso_actualizado' => $procesoVoucher->nombre . ' (ID: ' . $procesoVoucher->id_proceso_arbitraje . ')',
                'nuevo_proceso' => $nuevoProceso->nombre,
                'arbitraje_estado' => $arbitraje->estado,
                'debug_info' => [
                    'proceso_encontrado_id' => $procesoVoucher->id_proceso_arbitraje,
                    'proceso_encontrado_nombre' => $procesoVoucher->nombre,
                    'proceso_encontrado_estado_original' => $procesoVoucher->getOriginal('estado')
                ]
            ]
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->validator->errors()->first(),
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('=== ERROR COMPLETO AL RECHAZAR ===', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'arbitraje_id' => $id_arbitraje,
            'request_data' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar el rechazo: ' . $e->getMessage(),
            'error_details' => 'Ver logs para más información'
        ], 500);
    }
}

// Aceptar arbitraje (pasar a selección de árbitro)
public function aceptar(Request $request, $id_arbitraje)
{
    try {
        $request->validate([
            'comentario' => 'nullable|string|max:500'
        ], [
            'comentario.max' => 'El comentario no puede exceder 500 caracteres'
        ]);
        
        DB::beginTransaction();
        
        // 1. Verificar el arbitraje existe
        $arbitraje = Arbitraje::find($id_arbitraje);
        
        if (!$arbitraje) {
            return response()->json([
                'success' => false,
                'message' => 'Arbitraje no encontrado con ID: ' . $id_arbitraje
            ], 404);
        }
        
        Log::info('=== ACEPTAR ARBITRAJE ===', [
            'arbitraje_id' => $id_arbitraje,
            'estado_actual' => $arbitraje->estado,
            'comentario' => $request->comentario
        ]);
        
        // 2. Buscar el proceso de validación de voucher
        $procesoVoucher = ProcesoArbitraje::where([
            ['arbitraje_id', '=', $id_arbitraje],
            ['nombre', '=', 'Validacion de Voucher'],
            ['estado', '=', 'Iniciado']
        ])->first();
        
        // Si no se encuentra con exactitud, buscar con criterios flexibles
        if (!$procesoVoucher) {
            $procesoVoucher = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)
                ->where('nombre', 'like', '%voucher%')
                ->whereIn('estado', ['Iniciado', 'En proceso'])
                ->first();
                
            if (!$procesoVoucher) {
                $procesoVoucher = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)
                    ->where('estado', 'Iniciado')
                    ->first();
            }
        }
        
        if (!$procesoVoucher) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el proceso de validación de voucher'
            ], 404);
        }
        
        Log::info('=== PROCESO VOUCHER ENCONTRADO ===', [
            'proceso_id' => $procesoVoucher->id_proceso_arbitraje,
            'nombre' => $procesoVoucher->nombre,
            'estado_original' => $procesoVoucher->estado
        ]);
        
        // 3. Actualizar el proceso de validación de voucher
        $procesoVoucher->estado = 'Aceptado';
        $comentarioTexto = $request->comentario ? " [Comentario: " . $request->comentario . "]" : "";
        if (strpos($procesoVoucher->descripcion, '[ACEPTADO]') === false) {
            $procesoVoucher->descripcion .= " [ACEPTADO" . $comentarioTexto . "]";
        }
        $procesoVoucher->save();
        
        // 4. Crear nuevo proceso de selección de árbitro
        $nuevoProceso = ProcesoArbitraje::create([
            'arbitraje_id' => $id_arbitraje,
            'fecha' => now(),
            'nombre' => 'Seleccion de un Arbitro',
            'descripcion' => 'A espera de Seleccion de arbitro',
            'estado' => 'Iniciado'
        ]);
        
        // 5. Actualizar el arbitraje
        $arbitraje->estado = 'en proceso';
        $arbitraje->save();
        
        DB::commit();
        
        Log::info('=== ARBITRAJE ACEPTADO EXITOSAMENTE ===', [
            'arbitraje_id' => $id_arbitraje,
            'nuevo_estado' => $arbitraje->estado,
            'proceso_actualizado_id' => $procesoVoucher->id_proceso_arbitraje,
            'nuevo_proceso_id' => $nuevoProceso->id_proceso_arbitraje
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Arbitraje aceptado exitosamente. Se ha creado el proceso de selección de árbitro.',
            'data' => [
                'proceso_actualizado' => $procesoVoucher->nombre . ' (ID: ' . $procesoVoucher->id_proceso_arbitraje . ')',
                'nuevo_proceso' => $nuevoProceso->nombre,
                'arbitraje_estado' => $arbitraje->estado,
                'nuevo_proceso_id' => $nuevoProceso->id_proceso_arbitraje
            ]
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => $e->validator->errors()->first(),
        ], 422);
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('=== ERROR COMPLETO AL ACEPTAR ARBITRAJE ===', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'arbitraje_id' => $id_arbitraje,
            'request_data' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar la aceptación: ' . $e->getMessage()
        ], 500);
    }
}
}