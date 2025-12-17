<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Arbitraje;

class ArbitrajeController extends Controller
{
    public function registros()
    {
        return view('mesa-partes.arbitrajes.control');
    }
    
    public function obtenerArbitrajes()
    {
        try {
            $user = Auth::user();
            $userId = $user->id;
            
            // Obtener el DNI desde la tabla persona
            $persona = $user->persona;
            $userDni = $persona ? $persona->dni : null;
            
            \Log::info('Buscando arbitrajes para:', [
                'user_id' => $userId,
                'dni' => $userDni
            ]);
            
            // 1️⃣ BUSCAR ARBITRAJES CREADOS POR EL USUARIO
            $arbitrajesCreados = Arbitraje::with([
                'procesos' => function($query) {
                    $query->orderBy('fecha', 'desc');
                },
                'procesos.documentos',
                'personas'
            ])
            ->where('user_id', $userId)
            ->get();
            
            \Log::info('Arbitrajes creados encontrados: ' . $arbitrajesCreados->count());
            
            // 2️⃣ BUSCAR ARBITRAJES DONDE EL USUARIO ES DEMANDANTE/DEMANDADO (POR DNI)
            $arbitrajesPorDni = collect();
            
            if ($userDni) {
                $arbitrajesPorDni = Arbitraje::with([
                    'procesos' => function($query) {
                        $query->orderBy('fecha', 'desc');
                    },
                    'procesos.documentos',
                    'personas'
                ])
                ->whereHas('personas', function($query) use ($userDni) {
                    $query->where('dni', $userDni);
                })
                ->where('user_id', '!=', $userId) // Excluir los que ya tiene como creador
                ->get();
                
                \Log::info('Arbitrajes por DNI encontrados: ' . $arbitrajesPorDni->count());
            }
            
            // 3️⃣ COMBINAR AMBAS COLECCIONES Y ELIMINAR DUPLICADOS
            $arbitrajes = $arbitrajesCreados->merge($arbitrajesPorDni)->unique('id_arbitraje');
            
            // 4️⃣ ORDENAR POR FECHA MÁS RECIENTE
            $arbitrajes = $arbitrajes->sortByDesc('fecha_inicio')->values();
            
            \Log::info('Total de arbitrajes: ' . $arbitrajes->count());
            
            // 5️⃣ AGREGAR METADATA PARA IDENTIFICAR ROL DEL USUARIO EN CADA ARBITRAJE
            $arbitrajes = $arbitrajes->map(function($arbitraje) use ($userId, $userDni) {
                $esCreador = $arbitraje->user_id === $userId;
                
                $rolEnProceso = null;
                if (!$esCreador && $userDni) {
                    $personaEncontrada = $arbitraje->personas->firstWhere('dni', $userDni);
                    if ($personaEncontrada) {
                        $rolEnProceso = $personaEncontrada->tipo; // 'Demandante' o 'Demandado'
                    }
                }
                
                // Agregar metadata al objeto
                $arbitraje->es_creador = $esCreador;
                $arbitraje->rol_usuario = $esCreador ? 'Creador' : $rolEnProceso;
                
                return $arbitraje;
            });
            
            return response()->json([
                'success' => true,
                'arbitrajes' => $arbitrajes,
                'info' => [
                    'creados_por_usuario' => $arbitrajesCreados->count(),
                    'como_parte' => $arbitrajesPorDni->count(),
                    'total' => $arbitrajes->count(),
                    'dni_usuario' => $userDni
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al obtener arbitrajes:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los arbitrajes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
}