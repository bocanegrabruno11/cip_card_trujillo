<?php

namespace App\Http\Controllers;

use App\Models\CasillaElectronica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CasillaElectronicaController extends Controller
{
    public function index(Request $request)
    {
        // Iniciamos la consulta cargando todas las relaciones necesarias
        $query = CasillaElectronica::with(['emisor', 'arbitraje', 'jrd'])
            ->where('user_id', Auth::id());

        // Filtro por Rango de Fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_registro', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        }

        // Filtro por Tipo de Expediente
        if ($request->filled('tipo')) {
            if ($request->tipo === 'arbitraje') {
                $query->whereNotNull('arbitraje_id');
            } elseif ($request->tipo === 'jrd') {
                $query->whereNotNull('jrd_id');
            }
        }

        $notificaciones = $query->orderBy('fecha_registro', 'desc')->paginate(10);

        // Mantenemos los parámetros de búsqueda en la paginación
        $notificaciones->appends($request->all());

        return view('mesa-partes.casilla.index', compact('notificaciones'));
    }

    public function show($id)
    {
        // Cargamos las relaciones para poder vincular al expediente
        $notificacion = CasillaElectronica::with(['emisor', 'arbitraje', 'jrd'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        
        if ($notificacion->estado == 'no leido') {
            $notificacion->update(['estado' => 'leido']);
        }

        return view('mesa-partes.casilla.show', compact('notificacion'));
    }

    public function destroy($id)
    {
        $notificacion = CasillaElectronica::where('user_id', Auth::id())->findOrFail($id);
        $notificacion->delete();
        return redirect()->route('casilla.index')->with('success', 'Notificación eliminada.');
    }
}