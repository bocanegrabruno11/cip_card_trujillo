<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jrd;
use App\Models\Adjudicador;
use App\Models\ProcesoJrdPersona;
use App\Models\ActividadUsuario;
use Illuminate\Support\Facades\DB;

class AdminAdjudicadorVinculacionController extends Controller
{
    /**
     * Mostrar vista para vincular adjudicadores
     */
    public function index()
    {
        ActividadUsuario::log('Accedió a la vista de vinculación de adjudicadores', 'Admin - Vincular Adjudicadores');

        $jrds = Jrd::with(['personas', 'user.persona'])
            ->whereIn('estado', ['en proceso', 'iniciado', 'validando', 'observado'])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $adjudicadores = Adjudicador::with('users')->orderBy('apellidos')->get();

        return view('Admin.Adjudicadores.vinculacion-adjudicadores', compact('jrds', 'adjudicadores'));
    }

    /**
     * Vincular un adjudicador a un caso JRD
     */
    public function vincular(Request $request)
    {
        try {
            $request->validate([
                'jrd_id'         => 'required|exists:jrd,id_jrd',
                'adjudicador_id' => 'required|exists:adjudicadores,id'
            ]);

            DB::beginTransaction();

            $jrd = Jrd::findOrFail($request->jrd_id);
            $adjudicador = Adjudicador::findOrFail($request->adjudicador_id);

            // Verificar si ya existe una persona con este DNI
            $personaExistente = ProcesoJrdPersona::where('jrd_id', $request->jrd_id)
                ->where('dni', $adjudicador->dni)
                ->first();

            if ($personaExistente) {
                // Actualizar tipo a "Adjudicador"
                $personaExistente->tipo = 'Adjudicador';
                $personaExistente->save();

                ActividadUsuario::log(
                    'Vinculó al adjudicador ' . $adjudicador->nombre . ' ' . $adjudicador->apellidos .
                    ' al caso ' . ($jrd->numero_expediente ?? 'ID ' . $jrd->id_jrd) .
                    ' (persona existente por DNI)',
                    'Admin - Vincular Adjudicadores'
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Adjudicador vinculado correctamente al caso'
                ]);
            }

            // Crear nueva persona en el caso
            ProcesoJrdPersona::create([
                'jrd_id'            => $request->jrd_id,
                'dni'               => $adjudicador->dni,
                'nombres_completos' => $adjudicador->nombre . ' ' . $adjudicador->apellidos,
                'razon_social'      => null,
                'correo'            => $adjudicador->correo,
                'telefono'          => $adjudicador->telefono,
                'ruc'               => $adjudicador->ruc,
                'tipo'              => 'Adjudicador',
                'direccion'         => $adjudicador->direccion
            ]);

            ActividadUsuario::log(
                'Vinculó al adjudicador ' . $adjudicador->nombre . ' ' . $adjudicador->apellidos .
                ' al caso ' . ($jrd->numero_expediente ?? 'ID ' . $jrd->id_jrd) .
                ' (nueva persona creada)',
                'Admin - Vincular Adjudicadores'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Adjudicador vinculado correctamente al caso'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . collect($e->errors())->flatten()->first()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desvincular adjudicador
     */
    public function desvincular(Request $request)
    {
        try {
            $request->validate([
                'persona_id' => 'required|exists:procesos_jrd_personas,id_proceso_jrd_persona'
            ]);

            DB::beginTransaction();

            $persona = ProcesoJrdPersona::findOrFail($request->persona_id);
            $nombreAdjudicador = $persona->nombres_completos;
            $jrd = Jrd::find($persona->jrd_id);

            $persona->tipo = 'Ex Adjudicador';
            $persona->save();

            ActividadUsuario::log(
                'Desvinculó al adjudicador ' . $nombreAdjudicador .
                ' del caso ' . ($jrd ? $jrd->numero_expediente : 'ID ' . $persona->jrd_id),
                'Admin - Vincular Adjudicadores'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Adjudicador desvinculado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener adjudicadores vinculados a un caso
     */
    public function obtenerVinculados($jrdId)
    {
        try {
            $vinculados = ProcesoJrdPersona::where('jrd_id', $jrdId)
                ->where('tipo', 'Adjudicador')
                ->select(
                    'id_proceso_jrd_persona as id',
                    'dni',
                    'nombres_completos as nombre',
                    'tipo',
                    'correo',
                    'telefono'
                )
                ->orderBy('nombres_completos')
                ->get();

            $jrd = Jrd::find($jrdId);

            return response()->json([
                'success'     => true,
                'vinculados'  => $vinculados,
                'expediente'  => $jrd ? $jrd->numero_expediente : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}