<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arbitraje;
use App\Models\Arbitro;
use App\Models\ProcesoArbitrajePersona;
use App\Models\ActividadUsuario;
use Illuminate\Support\Facades\DB;

class AdminArbitroVinculacionController extends Controller
{
    /**
     * Mostrar vista para vincular árbitros
     */
    public function index()
    {
        ActividadUsuario::log('Accedió a la vista de vinculación de árbitros', 'Admin - Vincular Árbitros');
        
        $arbitrajes = Arbitraje::with(['personas', 'user.persona'])
            ->whereIn('estado', ['iniciado', 'en proceso', 'validando'])
            ->orderBy('fecha_inicio', 'desc')
            ->get();
            
        $arbitros = Arbitro::with('users')->orderBy('apellidos')->get();
        
        return view('Admin.Arbitros.vinculacion-arbitros', compact('arbitrajes', 'arbitros'));
    }

    /**
     * Vincular un árbitro a un caso usando la tabla procesos_arbitraje_personas
     */
    public function vincular(Request $request)
    {
        try {
            $request->validate([
                'arbitraje_id' => 'required|exists:arbitraje,id_arbitraje',
                'arbitro_id' => 'required|exists:arbitros,id'
            ]);

            DB::beginTransaction();

            $arbitraje = Arbitraje::findOrFail($request->arbitraje_id);
            $arbitro = Arbitro::findOrFail($request->arbitro_id);

            // Verificar si ya existe una persona con este DNI en el caso
            $personaExistente = ProcesoArbitrajePersona::where('arbitraje_id', $request->arbitraje_id)
                ->where('dni', $arbitro->dni)
                ->first();

            if ($personaExistente) {
                // Si ya existe, actualizar su tipo a "Arbitro"
                $personaExistente->tipo = 'Arbitro';
                $personaExistente->save();

                ActividadUsuario::log(
                    'Vinculó al árbitro ' . $arbitro->nombre . ' ' . $arbitro->apellidos . 
                    ' al caso ' . ($arbitraje->numero_expediente ?? 'ID ' . $arbitraje->id_arbitraje) .
                    ' (persona existente por DNI)',
                    'Admin - Vincular Árbitros'
                );

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Árbitro vinculado correctamente al caso'
                ]);
            }

            // Crear nueva persona en el caso
            ProcesoArbitrajePersona::create([
                'arbitraje_id' => $request->arbitraje_id,
                'dni' => $arbitro->dni,
                'nombres_apellidos' => $arbitro->nombre . ' ' . $arbitro->apellidos,
                'correo' => $arbitro->correo,
                'telefono' => $arbitro->telefono,
                'ruc' => $arbitro->ruc,
                'tipo' => 'Arbitro',
                'direccion' => $arbitro->direccion
            ]);

            ActividadUsuario::log(
                'Vinculó al árbitro ' . $arbitro->nombre . ' ' . $arbitro->apellidos . 
                ' al caso ' . ($arbitraje->numero_expediente ?? 'ID ' . $arbitraje->id_arbitraje) .
                ' (nueva persona creada)',
                'Admin - Vincular Árbitros'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Árbitro vinculado correctamente al caso'
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
     * Desvincular un árbitro de un caso (cambiar tipo a "Ex Arbitro")
     */
    public function desvincular(Request $request)
    {
        try {
            $request->validate([
                'persona_id' => 'required|exists:procesos_arbitraje_personas,id_proceso_arbitraje_persona'
            ]);

            DB::beginTransaction();

            $persona = ProcesoArbitrajePersona::findOrFail($request->persona_id);

            // Guardar el nombre del árbitro antes de cambiar
            $nombreArbitro = $persona->nombres_apellidos;
            $arbitraje = Arbitraje::find($persona->arbitraje_id);

            // Cambiar el tipo a "Ex Arbitro" (para no perder el historial)
            $persona->tipo = 'Ex Arbitro';
            $persona->save();

            ActividadUsuario::log(
                'Desvinculó al árbitro ' . $nombreArbitro . 
                ' del caso ' . ($arbitraje ? $arbitraje->numero_expediente : 'ID ' . $persona->arbitraje_id),
                'Admin - Vincular Árbitros'
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Árbitro desvinculado correctamente'
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
     * Obtener árbitros vinculados a un caso (personas con tipo Arbitro)
     */
    public function obtenerVinculados($arbitrajeId)
    {
        try {
            $vinculados = ProcesoArbitrajePersona::where('arbitraje_id', $arbitrajeId)
                ->where('tipo', 'Arbitro')
                ->select(
                    'id_proceso_arbitraje_persona as id',
                    'dni',
                    'nombres_apellidos as nombre',
                    'tipo',
                    'correo',
                    'telefono'
                )
                ->orderBy('nombres_apellidos')
                ->get();

            // Obtener el expediente del arbitraje
            $arbitraje = Arbitraje::find($arbitrajeId);

            return response()->json([
                'success' => true,
                'vinculados' => $vinculados,
                'expediente' => $arbitraje ? $arbitraje->numero_expediente : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}