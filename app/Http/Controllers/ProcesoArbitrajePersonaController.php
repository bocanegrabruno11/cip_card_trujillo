<?php

namespace App\Http\Controllers;

use App\Models\ProcesoArbitrajePersona;
use Illuminate\Http\Request;

class ProcesoArbitrajePersonaController extends Controller
{
    /**
     * Listar personas por arbitraje
     */
    public function index($arbitraje_id)
    {
        $personas = ProcesoArbitrajePersona::where('arbitraje_id', $arbitraje_id)
            ->get();

        return response()->json($personas);
    }

    /**
     * Registrar persona
     */
    public function store(Request $request)
    {
        $request->validate([
            'arbitraje_id' => 'required|exists:arbitraje,id_arbitraje',
            'dni' => 'required',
            'nombres' => 'required',
            'apellidos' => 'required',
            'tipo' => 'required'
        ]);

        $persona = ProcesoArbitrajePersona::create([
            'arbitraje_id' => $request->arbitraje_id,
            'dni' => $request->dni,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'correo' => $request->correo,
            'telefono' => $request->telefono,
            'ruc' => $request->ruc,
            'tipo' => $request->tipo
        ]);

        return response()->json($persona, 201);
    }

    /**
     * Mostrar una persona
     */
    public function show($id)
    {
        $persona = ProcesoArbitrajePersona::findOrFail($id);
        return response()->json($persona);
    }

    /**
     * Actualizar persona
     */
    public function update(Request $request, $id)
    {
        $persona = ProcesoArbitrajePersona::findOrFail($id);

        $persona->update($request->only([
            'dni',
            'nombres',
            'apellidos',
            'correo',
            'telefono',
            'ruc',
            'tipo'
        ]));

        return response()->json($persona);
    }

    /**
     * Eliminar persona
     */
    public function destroy($id)
    {
        $persona = ProcesoArbitrajePersona::findOrFail($id);
        $persona->delete();

        return response()->json([
            'message' => 'Persona eliminada correctamente'
        ]);
    }
}