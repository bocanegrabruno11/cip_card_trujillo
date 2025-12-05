<?php

namespace App\Http\Controllers;

use App\Models\TarifaEscala;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalculadoraController extends Controller
{
    public function index(Request $request)
    {
        $query = TarifaEscala::query();

        // Filtro por Tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Ordenar por Tipo y luego por Monto Mínimo para que salga en orden (A, B, C...)
        $tarifas = $query->orderBy('tipo')->where('activo', true)->orderBy('monto_min')->paginate(15);

        return view('gestion-contenido.calculadoras.index', compact('tarifas'));
    }

    public function create()
    {
        return view('gestion-contenido.calculadoras.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:arbitro_unico,tribunal_arbitral,gastos_administrativos',
            'rango_letra' => [
                'required',
                'string',
                'max:5',
                // Validación compuesta: único solo para este 'tipo'
                Rule::unique('tarifas_escalas')->where(function ($query) use ($request) {
                    return $query->where('tipo', $request->tipo);
                }),
            ],
            'monto_min' => 'required|numeric|min:0',
            'monto_max' => 'nullable|numeric|min:0|gt:monto_min',
            'monto_fijo' => 'required|numeric|min:0',
            'porcentaje_exceso' => 'required|numeric|min:0',
            'base_exceso' => 'required|numeric|min:0',
        ], [
            // Mensaje personalizado para que sea claro
            'rango_letra.unique' => 'Ya existe el rango "' . $request->rango_letra . '" para este tipo de tarifa.',
            'monto_max.gt' => 'El monto máximo debe ser mayor al mínimo.',
        ]);

        TarifaEscala::create($request->all());

        return redirect()->route('calculadoras-gestion.index')->with('success', 'Tarifa registrada correctamente.');
    }

    public function show($id)
    {
        $tarifa = TarifaEscala::findOrFail($id);
        return view('gestion-contenido.calculadoras.show', compact('tarifa'));
    }

    public function edit($id)
    {
        $tarifa = TarifaEscala::findOrFail($id);
        return view('gestion-contenido.calculadoras.edit', compact('tarifa'));
    }

    public function update(Request $request, $id)
    {
        $tarifa = TarifaEscala::findOrFail($id);

        $request->validate([
            'tipo' => 'required|in:arbitro_unico,tribunal_arbitral,gastos_administrativos',
            'rango_letra' => [
                'required',
                'string',
                'max:5',
                // Ignoramos el ID actual para permitir actualizar otros campos
                Rule::unique('tarifas_escalas')->ignore($id)->where(function ($query) use ($request) {
                    return $query->where('tipo', $request->tipo);
                }),
            ],
            'monto_min' => 'required|numeric|min:0',
            'monto_max' => 'nullable|numeric|min:0',
            'monto_fijo' => 'required|numeric|min:0',
            'porcentaje_exceso' => 'required|numeric|min:0',
            'base_exceso' => 'required|numeric|min:0',
        ], [
            'rango_letra.unique' => 'Ya existe el rango "' . $request->rango_letra . '" para este tipo de tarifa.',
        ]);

        $tarifa->update($request->all());

        return redirect()->route('calculadoras-gestion.index')->with('success', 'Tarifa actualizada correctamente.');
    }

    public function destroy($id)
    {
        $tarifa = TarifaEscala::findOrFail($id);
        
        // 1. Contar cuántos registros de ESTE TIPO quedarían si borramos este
        $restantes = TarifaEscala::where('tipo', $tarifa->tipo)
                                ->where('id', '!=', $id) // Excluyendo el actual
                                ->count();

        // 2. Si no va a quedar ninguno, bloqueamos la eliminación
        if ($restantes == 0) {
            return back()->withErrors([
                'error' => 'No puedes eliminar la única escala existente para "' . $tarifa->tipo_legible . '". La calculadora dejaría de funcionar. Edítala en su lugar.'
            ]);
        }

        // 3. Si hay más registros, permitimos eliminar pero con advertencia visual (Flash message)
        $tarifa->update([
            'activo' => false
        ]);

        return back()->with('warning', 'Registro eliminado. ¡ATENCIÓN! Verifica que no hayan quedado "huecos" en los montos (ej: del rango A al C sin pasar por el B). Revisa los rangos Mín/Máx de los registros restantes.');
    }
}