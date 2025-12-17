<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jrd;
use App\Models\ProcesoJrdPersona;
use App\Models\User;
use App\Models\Persona;

class AdminJrdController extends Controller
{
    public function obtenerJrd(Request $request)
    {
        try {
            $dni = $request->query('dni');
            
            $query = Jrd::with(['personas', 'user.persona'])
                ->orderBy('fecha_inicio', 'desc');
            
            // Filtrar por DNI si se proporciona
            if ($dni) {
                $query->where(function($q) use ($dni) {
                    // Buscar en personas involucradas
                    $q->whereHas('personas', function($query) use ($dni) {
                        $query->where('dni', 'LIKE', "%{$dni}%");
                    });
                    
                    // O buscar por DNI del creador
                    $q->orWhereHas('user.persona', function($query) use ($dni) {
                        $query->where('dni', 'LIKE', "%{$dni}%");
                    });
                });
            }
            
            $jrdList = $query->get();
            
            // Formatear respuesta
            $jrdFormateados = $jrdList->map(function($jrd) {
                // Obtener información del creador
                $creadorNombre = 'N/A';
                $creadorDni = 'N/A';
                
                if ($jrd->user && $jrd->user->persona) {
                    $creadorNombre = $jrd->user->persona->nombres . ' ' . $jrd->user->persona->apellidos;
                    $creadorDni = $jrd->user->persona->dni;
                }
                
                return [
                    'id_jrd' => $jrd->id_jrd,
                    'nombre_materia' => $jrd->nombre_materia,
                    'descripcion' => $jrd->descripcion,
                    'fecha_inicio' => $jrd->fecha_inicio,
                    'fecha_finalizacion' => $jrd->fecha_finalizacion,
                    'estado' => $jrd->estado,
                    'creador_nombre' => $creadorNombre,
                    'creador_dni' => $creadorDni,
                    'personas' => $jrd->personas->map(function($persona) {
                        return [
                            'dni' => $persona->dni,
                            'tipo' => $persona->tipo
                        ];
                    })
                ];
            });
            
            return response()->json([
                'success' => true,
                'jrd' => $jrdFormateados
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error obteniendo JRD para admin:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener JRD'
            ], 500);
        }
    }
    
    public function detalle($id)
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
            
            return view('Admin.jrd-detalle', compact('jrd'));
            
        } catch (\Exception $e) {
            return redirect()->route('admin.jrd')
                ->with('error', 'JRD no encontrado');
        }
    }
}