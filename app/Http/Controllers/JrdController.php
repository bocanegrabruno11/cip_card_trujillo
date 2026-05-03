<?php

namespace App\Http\Controllers;

use App\Models\Jrd;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JrdController extends Controller
{
    /**
     * Vista de Mis JPRD
     */
    public function misJrd()
    {
        return view('mesa-partes.mis-jprd');
    }

    /**
     * Obtener JPRD para Mesa de Partes (API)
     */
    public function obtenerJrdMesaPartes()
    {
        try {
            $user = Auth::user();
            
            Log::info('Obteniendo JPRD para Mesa de Partes', ['user_id' => $user->id]);
            
            $jrd = Jrd::with([
                'personas',
                'procesos' => function($query) {
                    $query->orderBy('fecha_creacion', 'desc')->with('etapa');
                },
                'procesos.documentos' => function($query) {
                    $query->orderBy('fecha_subida', 'desc');
                },
                'procesos.documentos.user'
            ])
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('personas', function($q) use ($user) {
                        $dniUsuario = $user->persona?->dni;
                        if ($dniUsuario) {
                            $q->where('dni', $dniUsuario);
                        } else {
                            $q->whereRaw('1=0');
                        }
                    });
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get();
            
            // Agregar campos calculados
            foreach ($jrd as $item) {
                $item->es_creador = ($item->user_id == $user->id);
                
                if ($item->es_creador) {
                    $item->rol_usuario = 'Creador';
                } else {
                    $dniUsuario = $user->persona?->dni;
                    $persona = $dniUsuario
                        ? $item->personas->firstWhere('dni', $dniUsuario)
                        : null;
                    $item->rol_usuario = $persona ? $persona->tipo : 'Observador';
                }
                
                // ✅ Agregar numero_expediente formateado
                $item->titulo_expediente = $item->numero_expediente 
                    ? "Expediente N° {$item->numero_expediente}" 
                    : ($item->nombre_materia ?? 'Sin expediente');
                
                // Agregar subido_por a cada documento
                foreach ($item->procesos as $proceso) {
                    foreach ($proceso->documentos as $documento) {
                        $documento->subido_por = $this->getSubidoPorAttribute($documento, $item);
                    }
                }
            }
            
            return response()->json([
                'success' => true,
                'jrd' => $jrd
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error al obtener JPRD para Mesa de Partes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información de quién subió el documento
     */
    private function getSubidoPorAttribute($documento, $jrd = null)
    {
        $user = $documento->user;
        if (!$user) return null;

        $nombreCompleto = trim(
            ($user->persona->nombres   ?? '') . ' ' .
            ($user->persona->apellidos ?? '')
        ) ?: ($user->name ?? 'Usuario');

        $rol = 'Usuario';
        if ($jrd) {
            $dniUploader = $user->persona->dni ?? null;
            $personaJrd  = $dniUploader
                ? $jrd->personas->firstWhere('dni', $dniUploader)
                : null;

            if ($personaJrd) {
                $rol = $personaJrd->tipo;
            } elseif ($jrd->user_id == $user->id) {
                $rol = 'Creador';
            } else {
                $rolSistema = strtolower($user->rol ?? '');
                if ($rolSistema === 'admin')        $rol = 'Administrador';
                elseif ($rolSistema === 'mesa_partes') $rol = 'Mesa de Partes';
            }
        }

        $rolConfig = [
            'Solicitante'    => ['color' => 'success',   'icono' => 'fa-user-check'],
            'Demandado'      => ['color' => 'warning',   'icono' => 'fa-user-shield'],
            'Contraparte'    => ['color' => 'danger',    'icono' => 'fa-user-slash'],
            'Tercero'        => ['color' => 'secondary', 'icono' => 'fa-user-friends'],
            'Demandante'     => ['color' => 'primary',   'icono' => 'fa-user-plus'],
            'Creador'        => ['color' => 'info',      'icono' => 'fa-user-tie'],
            'Administrador'  => ['color' => 'danger',    'icono' => 'fa-user-tie'],
            'Mesa de Partes' => ['color' => 'info',      'icono' => 'fa-building'],
            'Usuario'        => ['color' => 'secondary', 'icono' => 'fa-user'],
        ];

        $cfg = $rolConfig[$rol] ?? ['color' => 'secondary', 'icono' => 'fa-user'];

        return [
            'label'  => $rol,
            'color'  => $cfg['color'],
            'icono'  => $cfg['icono'],
            'nombre' => strtoupper($nombreCompleto),
        ];
    }
}