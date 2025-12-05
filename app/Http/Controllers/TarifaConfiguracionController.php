<?php

namespace App\Http\Controllers;

use App\Models\TarifaConfiguracion;
use Illuminate\Http\Request;

class TarifaConfiguracionController extends Controller
{
    public function index(Request $request)
    {
        $query = TarifaConfiguracion::query();

        // Filtro simple por clave o descripción
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('clave', 'like', "%$search%")
                  ->orWhere('descripcion', 'like', "%$search%");
        }

        $configs = $query->where('activo', true)->orderBy('clave')->paginate(10);

        return view('gestion-contenido.tarifas_config.index', compact('configs'));
    }

    public function create()
    {
        return view('gestion-contenido.tarifas_config.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave' => 'required|string|max:50|unique:tarifas_configuracion,clave',
            'valor' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
        ]);

        TarifaConfiguracion::create($request->all() + ['activo' => 1]);

        return redirect()->route('tarifas_config.index')->with('success', 'Configuración creada correctamente.');
    }

    public function edit($id)
    {
        $config = TarifaConfiguracion::findOrFail($id);
        return view('gestion-contenido.tarifas_config.edit', compact('config'));
    }

    public function update(Request $request, $id)
    {
        $config = TarifaConfiguracion::findOrFail($id);

        $request->validate([
            'clave' => 'required|string|max:50|unique:tarifas_configuracion,clave,' . $id,
            'valor' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $config->update($request->all());

        return redirect()->route('tarifas_config.index')->with('success', 'Valor actualizado correctamente.');
    }

    public function destroy($id)
    {
        $config = TarifaConfiguracion::findOrFail($id);
        $config->update([
            'activo' => false
        ]);

        return back()->with('success', 'Configuración eliminada.');
    }
}