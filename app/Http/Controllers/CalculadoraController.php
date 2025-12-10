<?php

namespace App\Http\Controllers;

use App\Models\TarifaEscala;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalculadoraController extends Controller
{
    public function index(Request $request)
    {
        $query = TarifaEscala::query()->where('activo', true);

        // 1. Filtros
        if ($request->filled('tipo_calculadora')) {
            $query->where('tipo_calculadora', $request->tipo_calculadora);
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // 2. Si hay filtros aplicados, paginamos normal
        if ($request->filled('tipo_calculadora') || $request->filled('tipo')) {
            $tarifas = $query->orderBy('tipo_calculadora')
                             ->orderBy('tipo')
                             ->orderBy('monto_min')
                             ->paginate(20);
            
            // Enviamos una variable para saber que estamos en modo filtrado
            return view('gestion-contenido.calculadoras.index', compact('tarifas'));
        }
        
        // 3. Si NO hay filtros, obtenemos todo agrupado para mostrarlo bonito
        // Usamos get() en lugar de paginate() para poder separar manualmente en la vista,
        // o podríamos hacer dos queries. Lo más limpio para una tabla de configuración
        // (que no suele tener miles de registros) es traer todo.
        $tarifasArbitraje = TarifaEscala::where('activo', true)
            ->where('tipo_calculadora', 'servicio_arbitral')
            ->orderBy('tipo')
            ->orderBy('monto_min')
            ->get();

        $tarifasJunta = TarifaEscala::where('activo', true)
            ->where('tipo_calculadora', 'junta_prevencion')
            ->orderBy('tipo')
            ->orderBy('monto_min')
            ->get();

        return view('gestion-contenido.calculadoras.index', compact('tarifasArbitraje', 'tarifasJunta'));
    }

    public function create()
    {
        return view('gestion-contenido.calculadoras.create');
    }

    public function store(Request $request)
    {
        // Función de validación personalizada para reutilizar lógica de solapamiento
        $validarSolapamiento = function ($attribute, $value, $fail) use ($request) {
            $nuevoMin = $request->monto_min;
            $nuevoMax = $request->monto_max; // Puede ser null (infinito)

            // 1. Busamos rangos existentes con el mismo TIPO y CALCULADORA
            $query = TarifaEscala::where('activo', true)
                ->where('tipo', $request->tipo)
                ->where('tipo_calculadora', $request->tipo_calculadora);

            $rangosExistentes = $query->get(['id', 'monto_min', 'monto_max']);

            foreach ($rangosExistentes as $rango) {
                // Definir infinitos si es null
                $dbMax = $rango->monto_max ?? PHP_FLOAT_MAX; // Si es null en BD, es infinito
                $inputMax = $nuevoMax ?? PHP_FLOAT_MAX;      // Si es null en input, es infinito

                // LÓGICA MATEMÁTICA DE SOLAPAMIENTO:
                // Dos rangos [A, B] y [C, D] se solapan si: A <= D y B >= C
                // En tu caso, si el rango A termina en 10 y B empieza en 10, chocan.
                
                if ($nuevoMin <= $dbMax && $inputMax >= $rango->monto_min) {
                    $maxLegible = $rango->monto_max ?? 'En adelante';
                    $fail("El rango ingresado (S/ $nuevoMin - " . ($nuevoMax ?? '∞') . ") choca con el rango existente: S/ {$rango->monto_min} - S/ $maxLegible.");
                    return; // Detener al encontrar el primer error
                }
            }
        };

        $request->validate([
            'tipo_calculadora' => 'required|in:servicio_arbitral,junta_prevencion',
            'tipo' => 'required|in:arbitro_unico,tribunal_arbitral,gastos_administrativos',
            'rango_letra' => [
                'required', 'string', 'max:5',
                Rule::unique('tarifas_escalas')->where('activo', true)->where(function ($query) use ($request) {
                    return $query->where('tipo', $request->tipo)
                                ->where('tipo_calculadora', $request->tipo_calculadora);
                }),
            ],
            'monto_min' => [
                'required', 'numeric', 'min:0',
                $validarSolapamiento // <--- AQUÍ APLICAMOS LA REGLA PERSONALIZADA
            ],
            'monto_max' => 'nullable|numeric|min:0|gt:monto_min',
            'monto_fijo' => 'required|numeric|min:0',
            'porcentaje_exceso' => 'required|numeric|min:0',
            'base_exceso' => 'required|numeric|min:0',
        ], [
            'rango_letra.unique' => 'Ya existe la letra "' . $request->rango_letra . '" para este tipo y calculadora.',
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

        // Función de validación (Casi idéntica a Store, pero excluyendo el ID actual)
        $validarSolapamientoUpdate = function ($attribute, $value, $fail) use ($request, $id) {
            $nuevoMin = $request->monto_min;
            $nuevoMax = $request->monto_max; 

            $query = TarifaEscala::where('tipo', $request->tipo)
                ->where('activo', true)
                ->where('tipo_calculadora', $request->tipo_calculadora)
                ->where('id', '!=', $id); // <--- IMPORTANTE: Excluir el registro que editamos

            $rangosExistentes = $query->get(['id', 'monto_min', 'monto_max']);

            foreach ($rangosExistentes as $rango) {
                $dbMax = $rango->monto_max ?? PHP_FLOAT_MAX;
                $inputMax = $nuevoMax ?? PHP_FLOAT_MAX;

                if ($nuevoMin <= $dbMax && $inputMax >= $rango->monto_min) {
                    $maxLegible = $rango->monto_max ?? 'En adelante';
                    $fail("El rango entra en conflicto con: S/ {$rango->monto_min} - S/ $maxLegible.");
                    return;
                }
            }
        };

        $request->validate([
            'tipo_calculadora' => 'required|in:servicio_arbitral,junta_prevencion',
            'tipo' => 'required|in:arbitro_unico,tribunal_arbitral,gastos_administrativos',
            'rango_letra' => [
                'required', 'string', 'max:5',
                Rule::unique('tarifas_escalas')->ignore($id)->where('activo', true)->where(function ($query) use ($request) {
                    return $query->where('tipo', $request->tipo)
                                ->where('tipo_calculadora', $request->tipo_calculadora);
                }),
            ],
            'monto_min' => [
                'required', 'numeric', 'min:0',
                $validarSolapamientoUpdate // <--- REGLA PARA UPDATE
            ],
            'monto_max' => 'nullable|numeric|min:0', // Quitamos gt:monto_min aquí a veces si JS no ayuda, pero mejor dejarlo si quieres consistencia, aunque a veces da problemas si solo editas uno. Lo dejo simple.
            'monto_fijo' => 'required|numeric|min:0',
            'porcentaje_exceso' => 'required|numeric|min:0',
            'base_exceso' => 'required|numeric|min:0',
        ], [
            'rango_letra.unique' => 'Ya existe la letra "' . $request->rango_letra . '" para este tipo y calculadora.',
        ]);

        // Validación extra manual para monto_max > monto_min en update (si ambos se envían)
        if (!is_null($request->monto_max) && $request->monto_max <= $request->monto_min) {
            return back()->withErrors(['monto_max' => 'El monto máximo debe ser mayor al mínimo.'])->withInput();
        }

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