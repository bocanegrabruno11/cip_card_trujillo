<?php

namespace App\Http\Controllers;

use App\Models\EtapaArbitral;
use Illuminate\Http\Request;

class EtapaArbitralController extends Controller
{
    // Vista única
    public function index()
    {
        $etapas = EtapaArbitral::all(); // activos e inactivos
        return view('Admin.crear-arbitraje', compact('etapas'));
    }

    // Guardar
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100'
        ]);

        EtapaArbitral::create([
            'nombre' => $request->nombre,
            'estado' => 1
        ]);

        return redirect()->route('Admin.etapas.index');
    }

    // Actualizar
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:100'
        ]);

        $etapa = EtapaArbitral::findOrFail($id);

        $etapa->update([
            'nombre' => $request->nombre
        ]);

        return redirect()->route('Admin.etapas.index');
    }

    // Eliminar lógico
    public function destroy($id)
    {
        $etapa = EtapaArbitral::findOrFail($id);

        $etapa->update([
            'estado' => 0
        ]);

        return redirect()->route('Admin.etapas.index');
    }

    // Cambiar estado (activar/desactivar)
    public function toggle($id)
    {
        $etapa = EtapaArbitral::findOrFail($id);

        $etapa->estado = $etapa->estado == 1 ? 0 : 1;
        $etapa->save();

        return redirect()->route('Admin.etapas.index');
    }
}