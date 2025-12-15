<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arbitraje;
use Illuminate\Support\Facades\Log;

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
            
            // CORRECCIÓN: Cambia 'admin.arbitraje-detalle' por 'Admin.arbitraje-detalle'
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
}