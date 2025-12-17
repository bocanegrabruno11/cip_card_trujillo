<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jrd;
use App\Models\ProcesoJrd;
use App\Models\ProcesoJrdPersona;
use App\Models\ProcesoJrdDocumento;

class JrdController extends Controller
{
    public function obtenerJrd()
    {
        try {
            $user = Auth::user();
            $userDni = $user->persona->dni ?? null;
            
            if (!$userDni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no tiene DNI registrado'
                ]);
            }
            
            // Obtener JRD donde el usuario es creador
            $jrdComoCreador = Jrd::with(['personas', 'procesos.documentos'])
                ->where('user_id', $user->id)
                ->get();
            
            // Obtener JRD donde el usuario está en la tabla de personas
            $jrdComoPersona = Jrd::with(['personas', 'procesos.documentos'])
                ->whereHas('personas', function($query) use ($userDni) {
                    $query->where('dni', $userDni);
                })
                ->get();
            
            // Combinar y eliminar duplicados
            $allJrd = $jrdComoCreador->merge($jrdComoPersona)->unique('id_jrd')->values();
            
            // Formatear respuesta
            $jrdFormateados = $allJrd->map(function($jrd) use ($user, $userDni) {
                $esCreador = $jrd->user_id == $user->id;
                
                // Determinar rol del usuario
                $rolUsuario = 'Solicitante'; // Por defecto
                if ($esCreador) {
                    $rolUsuario = 'Creador';
                } else {
                    $personaUsuario = $jrd->personas->firstWhere('dni', $userDni);
                    $rolUsuario = $personaUsuario->tipo ?? 'Solicitante';
                }
                
                return [
                    'id_jrd' => $jrd->id_jrd,
                    'nombre_materia' => $jrd->nombre_materia,
                    'descripcion' => $jrd->descripcion,
                    'fecha_inicio' => $jrd->fecha_inicio,
                    'fecha_finalizacion' => $jrd->fecha_finalizacion,
                    'estado' => $jrd->estado,
                    'es_creador' => $esCreador,
                    'rol_usuario' => $rolUsuario,
                    'personas' => $jrd->personas->map(function($persona) {
                        return [
                            'dni' => $persona->dni,
                            'tipo' => $persona->tipo
                        ];
                    }),
                    'procesos' => $jrd->procesos->map(function($proceso) {
                        return [
                            'nombre' => $proceso->nombre,
                            'descripcion' => $proceso->descripcion,
                            'fecha' => $proceso->fecha,
                            'estado' => $proceso->estado,
                            'documentos' => $proceso->documentos->map(function($doc) {
                                return [
                                    'nombre_original' => $doc->nombre_original,
                                    'ruta_archivo' => $doc->ruta_archivo,
                                    'tipo_documento' => $doc->tipo_documento
                                ];
                            })
                        ];
                    })
                ];
            });
            
            return response()->json([
                'success' => true,
                'jrd' => $jrdFormateados
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error obteniendo JRD:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener JRD'
            ], 500);
        }
    }
}