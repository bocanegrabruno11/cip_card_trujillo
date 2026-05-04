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
        $totalRegistrados = AsistenteCipcdll::where('estado', 'aprobado')->count();
        
        if ($totalRegistrados >= 260) {
            return response()->json([
                'success' => false,
                'message' => 'Lo sentimos, el aforo máximo de 260 personas ya ha sido alcanzado.'
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
        $totalAprobados = AsistenteCipcdll::where('estado', 'registrado')->count();
        
        if ($totalAprobados >= 260) {
            // Evento lleno - mostrar vista de cupo completo
            return view('eventoscipcdll/evento-lleno');
        } else {
            // Evento disponible - mostrar formulario normal
            return view('eventoscipcdll/eventoscipcdll');
        }
    }
}