<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Arbitraje;
use App\Models\Jrd;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $userDni = $user->persona->dni ?? null;

        /* =========================
           ARBITRAJES DEL USUARIO
        ==========================*/

        $arbitrajes = Arbitraje::where(function ($query) use ($userId, $userDni) {
                $query->where('user_id', $userId);

                if ($userDni) {
                    $query->orWhereHas('personas', function ($q) use ($userDni) {
                        $q->where('dni', $userDni);
                    });
                }
            })->get();

        $arbitrajesRecibidos   = $arbitrajes->count();
        $arbitrajesPendientes  = $arbitrajes->where('estado', 'Pendiente')->count();
        $arbitrajesRevision    = $arbitrajes->where('estado', 'En revisión')->count();
        $arbitrajesConcluidos  = $arbitrajes->where('estado', 'Concluido')->count();

        /* =========================
           JRD DEL USUARIO
        ==========================*/

        $jrds = Jrd::where(function ($query) use ($userId, $userDni) {
                $query->where('user_id', $userId);

                if ($userDni) {
                    $query->orWhereHas('personas', function ($q) use ($userDni) {
                        $q->where('dni', $userDni);
                    });
                }
            })->get();

        // Calcular estadísticas según el estado real del JRD
        $jrdPendientes = $this->calcularJrdPendientes($jrds);
        $jrdRevision   = $this->calcularJrdEnRevision($jrds);
        $jrdConcluidos = $this->calcularJrdConcluidos($jrds);

        /* =========================
           VISTA
        ==========================*/

        return view('mesa-partes.dashboard', compact(
            'arbitrajesRecibidos',
            'arbitrajesPendientes',
            'arbitrajesRevision',
            'arbitrajesConcluidos',
            'jrdPendientes',
            'jrdRevision',
            'jrdConcluidos'
        ));
    }

    /**
     * Calcula la cantidad de JRD pendientes
     * Incluye: validando, pendiente, iniciado
     */
    private function calcularJrdPendientes($jrds)
    {
        return $jrds->filter(function ($jrd) {
            $estado = strtolower(trim($jrd->estado));
            return in_array($estado, ['validando', 'pendiente', 'iniciado']);
        })->count();
    }

    /**
     * Calcula la cantidad de JRD en revisión/proceso
     * Incluye: en proceso, en revision
     */
    private function calcularJrdEnRevision($jrds)
    {
        return $jrds->filter(function ($jrd) {
            $estado = strtolower(trim($jrd->estado));
            return in_array($estado, ['en proceso', 'en revision', 'en revisión']);
        })->count();
    }

    /**
     * Calcula la cantidad de JRD concluidos
     * Incluye: terminado, concluido, finalizado
     */
    private function calcularJrdConcluidos($jrds)
    {
        return $jrds->filter(function ($jrd) {
            $estado = strtolower(trim($jrd->estado));
            return in_array($estado, ['terminado', 'concluido', 'finalizado', 'completado']);
        })->count();
    }

    /**
     * Obtiene estadísticas detalladas de un JRD específico
     * (Método adicional por si lo necesitas)
     */
    public function obtenerEstadisticasJrd($id_jrd)
    {
        try {
            $jrd = Jrd::with('procesos')->findOrFail($id_jrd);
            
            $procesoActual = $jrd->procesos()
                ->where('estado', '!=', 'Finalizado')
                ->orderBy('fecha', 'desc')
                ->first();

            $procesosFinalizados = $jrd->procesos()
                ->where('estado', 'Finalizado')
                ->count();

            $totalProcesos = $jrd->procesos()->count();

            $porcentajeAvance = $totalProcesos > 0 
                ? round(($procesosFinalizados / $totalProcesos) * 100, 2) 
                : 0;

            return [
                'jrd_id' => $jrd->id_jrd,
                'estado_jrd' => $jrd->estado,
                'proceso_actual' => $procesoActual ? $procesoActual->nombre : 'Sin proceso activo',
                'procesos_finalizados' => $procesosFinalizados,
                'procesos_totales' => $totalProcesos,
                'porcentaje_avance' => $porcentajeAvance,
                'fecha_inicio' => $jrd->fecha_inicio,
                'fecha_finalizacion' => $jrd->fecha_finalizacion
            ];

        } catch (\Exception $e) {
            return [
                'error' => 'No se pudo obtener las estadísticas del JRD',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene el conteo de JRD por estado
     * (Método adicional para reportes o análisis)
     */
    public function obtenerConteoPorEstado($userId = null, $userDni = null)
    {
        $userId = $userId ?? Auth::id();
        $userDni = $userDni ?? Auth::user()->persona->dni ?? null;

        $jrds = Jrd::where(function ($query) use ($userId, $userDni) {
                $query->where('user_id', $userId);

                if ($userDni) {
                    $query->orWhereHas('personas', function ($q) use ($userDni) {
                        $q->where('dni', $userDni);
                    });
                }
            })->get();

        // Agrupar por estado normalizado
        $conteo = [
            'validando' => 0,
            'iniciado' => 0,
            'en_proceso' => 0,
            'terminado' => 0,
            'rechazado' => 0,
            'otros' => 0
        ];

        foreach ($jrds as $jrd) {
            $estado = strtolower(trim($jrd->estado));
            
            if ($estado === 'validando') {
                $conteo['validando']++;
            } elseif ($estado === 'iniciado') {
                $conteo['iniciado']++;
            } elseif (in_array($estado, ['en proceso', 'en revision', 'en revisión'])) {
                $conteo['en_proceso']++;
            } elseif (in_array($estado, ['terminado', 'concluido', 'finalizado'])) {
                $conteo['terminado']++;
            } elseif ($estado === 'rechazado') {
                $conteo['rechazado']++;
            } else {
                $conteo['otros']++;
            }
        }

        return $conteo;
    }

    /**
     * Obtiene el proceso actual de cada JRD del usuario
     * (Método adicional para vista detallada)
     */
    public function obtenerProcesosActivos()
    {
        $user = Auth::user();
        $userId = $user->id;
        $userDni = $user->persona->dni ?? null;

        $jrds = Jrd::with(['procesos' => function ($query) {
                $query->where('estado', '!=', 'Finalizado')
                      ->orderBy('fecha', 'desc');
            }])
            ->where(function ($query) use ($userId, $userDni) {
                $query->where('user_id', $userId);

                if ($userDni) {
                    $query->orWhereHas('personas', function ($q) use ($userDni) {
                        $q->where('dni', $userDni);
                    });
                }
            })
            ->whereIn('estado', ['en proceso', 'en revision', 'iniciado'])
            ->get();

        $procesosActivos = [];

        foreach ($jrds as $jrd) {
            $procesoActual = $jrd->procesos->first();
            
            if ($procesoActual) {
                $procesosActivos[] = [
                    'jrd_id' => $jrd->id_jrd,
                    'jrd_nombre' => $jrd->nombre_materia,
                    'jrd_estado' => $jrd->estado,
                    'proceso_nombre' => $procesoActual->nombre,
                    'proceso_estado' => $procesoActual->estado,
                    'proceso_fecha' => $procesoActual->fecha
                ];
            }
        }

        return $procesosActivos;
    }
}
