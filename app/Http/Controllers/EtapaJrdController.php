<?php

namespace App\Http\Controllers;

use App\Models\EtapaJrd;
use Illuminate\Http\Request;

class EtapaJrdController extends Controller
{
    // Vista única
    public function index()
    {
        $etapas = EtapaJrd::all(); // activos e inactivos
        return view('Admin.crear-jrd', compact('etapas'));
    }

    // Guardar
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:100'
        ]);

        EtapaJrd::create([
            'nombre' => $request->nombre,
            'estado' => 1
        ]);

        return redirect()->route('Admin.jrd.etapas.index');
    }

    // Actualizar
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:100'
        ]);

        $etapa = EtapaJrd::findOrFail($id);

        $etapa->update([
            'nombre' => $request->nombre
        ]);

        return redirect()->route('Admin.jrd.etapas.index');
    }

    // Eliminar lógico
    public function destroy($id)
    {
        $etapa = EtapaJrd::findOrFail($id);

        $etapa->update([
            'estado' => 0
        ]);

        return redirect()->route('Admin.jrd.etapas.index');
    }

    // Cambiar estado (activar/desactivar)
    public function toggle($id)
    {
        $etapa = EtapaJrd::findOrFail($id);

        $etapa->estado = $etapa->estado == 1 ? 0 : 1;
        $etapa->save();

        return redirect()->route('Admin.jrd.etapas.index');
    }
}