<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PadronCipcdll;
use App\Models\AsistenteCipcdll;

class CipcdllController extends Controller
{
    /**
     * 🔍 FUNCION 1: Validar CIP y DNI contra padrón
     */
    public function validarCipDni(Request $request)
    {
        $cip = $request->cip;
        $dni = $request->dni;

        $persona = PadronCipcdll::where('cip', $cip)->first();

        if (!$persona) {
            return response()->json([
                'success' => false,
                'message' => 'El CIP no existe en el padrón'
            ]);
        }

        if ($persona->dni != $dni) {
            return response()->json([
                'success' => false,
                'message' => 'El DNI no coincide con el CIP'
            ]);
        }

        return response()->json([
            'success' => true,
            'nombres' => $persona->nombres,
            'apellidos' => $persona->apellidos,
            'capitulo' => $persona->capitulo
        ]);
    }

    /**
     * 📝 FUNCION 2: Registrar asistente
     */
    public function registrarAsistente(Request $request)
    {
        $cip = $request->cip;
        
        // 🔥 NUEVO: Verificar aforo antes de registrar
        $totalRegistrados = AsistenteCipcdll::count();

        if ($totalRegistrados >=326) {
            return response()->json([
                'success' => false,
                'message' => 'Lo sentimos, el aforo máximo ya ha sido alcanzado.'
            ]);
        }

        // 🔍 Verificar si ya existe registro
        $existe = AsistenteCipcdll::where('cip', $cip)->first();

        if ($existe) {
            if ($existe->estado == 'registrado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Usted ya se encuentra registrado'
                ]);
            }

            if ($existe->estado == 'rechazado') {
                $existe->update([
                    'nombres' => $request->nombres,
                    'apellidos' => $request->apellidos,
                    'capitulo' => $request->capitulo,
                    'dni' => $request->dni,
                    'celular' => $request->celular,
                    'correo' => $request->correo,
                    'estado' => 'registrado'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Registro actualizado correctamente'
                ]);
            }

            if ($existe->estado == 'aprobado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Usted ya fue aprobado, no puede registrarse nuevamente'
                ]);
            }
        }

        // ✅ Nuevo registro
        AsistenteCipcdll::create([
            'cip' => $request->cip,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'capitulo' => $request->capitulo,
            'dni' => $request->dni,
            'celular' => $request->celular,
            'correo' => $request->correo,
            'estado' => 'registrado'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registro exitoso'
        ]);
    }

    /**
     * 🔄 FUNCION 3: Cambiar estado (aprobado / rechazado)
     */
    public function cambiarEstado(Request $request)
    {
        $asistente = AsistenteCipcdll::find($request->id);

        if (!$asistente) {
            return response()->json([
                'success' => false,
                'message' => 'Asistente no encontrado'
            ]);
        }

        $estado = $request->estado;

        if (!in_array($estado, ['aprobado', 'rechazado'])) {
            return response()->json([
                'success' => false,
                'message' => 'Estado inválido'
            ]);
        }

        $asistente->estado = $estado;
        $asistente->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
    }

    /**
     * 🎯 NUEVA FUNCION 4: Verificar si el evento está lleno
     * Esta función se usará desde la ruta GET /eventoscipcdll
     */
    public function verificarEvento()
    {
         $totalAprobados = AsistenteCipcdll::count();
        
        if ($totalAprobados >= 326) {
            // Evento lleno - mostrar vista de cupo completo
            return view('eventoscipcdll/evento-lleno');
        } else {
            // Evento disponible - mostrar formulario normal
            return view('eventoscipcdll/eventoscipcdll');
        }
    }
/**
 * ✅ FUNCION 8: Validar múltiples asistencias en BATCH (lotes)
 * Recibe un array de CIPs con sus decisiones
 */
public function validarAsistenciaBatch(Request $request)
{
    try {
        $validaciones = $request->validaciones; // Array de {cip, respuesta}
        
        if (empty($validaciones) || !is_array($validaciones)) {
            return response()->json([
                'success' => false,
                'message' => 'No se enviaron validaciones'
            ]);
        }
        
        $resultados = [];
        $actualizados = 0;
        $errores = [];
        
        foreach ($validaciones as $item) {
            $cip = $item['cip'];
            $respuesta = strtolower($item['respuesta']);
            
            $asistente = AsistenteCipcdll::where('cip', $cip)->first();
            
            if (!$asistente) {
                $errores[] = "CIP {$cip} no encontrado";
                continue;
            }
            
            if ($respuesta === 'si') {
                $asistente->update([
                    'estado' => 'aprobado',
                    'asistio' => null
                ]);
                $actualizados++;
                $resultados[] = ['cip' => $cip, 'status' => 'aprobado'];
            } elseif ($respuesta === 'no') {
                $asistente->update([
                    'estado' => 'rechazado',
                    'asistio' => 0
                ]);
                $actualizados++;
                $resultados[] = ['cip' => $cip, 'status' => 'rechazado'];
            } else {
                $errores[] = "Respuesta inválida para CIP {$cip}: {$respuesta}";
            }
        }
        
        return response()->json([
            'success' => true,
            'actualizados' => $actualizados,
            'errores' => $errores,
            'resultados' => $resultados,
            'message' => "{$actualizados} registro(s) actualizados correctamente" . 
                        (count($errores) ? ". Errores: " . implode(", ", $errores) : "")
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error en validarAsistenciaBatch: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error interno: ' . $e->getMessage()
        ], 500);
    }
}
/**
 * 📋 FUNCION 6: Listar asistentes por estado
 */
// ❌ listarPorEstado no pagina — el JS espera data.last_page, data.current_page, data.total
public function listarPorEstado($estado)
{
    if (!in_array($estado, ['aprobado', 'rechazado', 'registrado'])) {
        return response()->json(['success' => false, 'message' => 'Estado inválido']);
    }

    // ✅ Cambia ->get() por ->paginate(10) para que el JS funcione
    $asistentes = AsistenteCipcdll::where('estado', $estado)
        ->orderBy('apellidos')
        ->paginate(10);

    return response()->json($asistentes); // paginate() ya genera last_page, current_page, total, data
}
 public function listarTodosAgrupados()
    {
        $aprobados   = AsistenteCipcdll::where('estado', 'aprobado')->count();
        $rechazados  = AsistenteCipcdll::where('estado', 'rechazado')->count();
        $registrados = AsistenteCipcdll::where('estado', 'registrado')->count();

        $todos = AsistenteCipcdll::orderBy('apellidos')->paginate(20);

        return response()->json([
            'resumen' => [
                'aprobados'   => $aprobados,
                'rechazados'  => $rechazados,
                'registrados' => $registrados,
                'total'       => $aprobados + $rechazados + $registrados,
            ],
            'data' => $todos
        ]);
    }
    /**
 * ✅ FUNCION 7: Ver TODOS los asistentes APROBADOS (sin paginación)
 */
public function verPendientes()
{
    try {
        \Log::info('verPendientes llamado');
        $pendientes = AsistenteCipcdll::where('estado', 'registrado')
            ->orderBy('apellidos')
            ->get();
        \Log::info('Pendientes encontrados: ' . $pendientes->count());
        return response()->json(['data' => $pendientes]);
    } catch (\Exception $e) {
        \Log::error('Error en verPendientes: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function verAprobados()
{
    try {
        \Log::info('verAprobados llamado');
        $aprobados = AsistenteCipcdll::where('estado', 'aprobado')
            ->orderBy('apellidos')
            ->get();
        \Log::info('Aprobados encontrados: ' . $aprobados->count());
        return response()->json(['data' => $aprobados]);
    } catch (\Exception $e) {
        \Log::error('Error en verAprobados: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function verRechazados()
{
    try {
        \Log::info('verRechazados llamado');
        $rechazados = AsistenteCipcdll::where('estado', 'rechazado')
            ->orderBy('apellidos')
            ->get();
        \Log::info('Rechazados encontrados: ' . $rechazados->count());
        return response()->json(['data' => $rechazados]);
    } catch (\Exception $e) {
        \Log::error('Error en verRechazados: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}