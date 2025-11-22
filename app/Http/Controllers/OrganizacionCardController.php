<?php

namespace App\Http\Controllers;

use App\Models\OrganizacionCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizacionCardController extends Controller
{
    public function index(Request $request)
    {
        $query = OrganizacionCard::query();

        // Filtro por Grupo
        $query->when($request->filled('grupo'), function ($q) use ($request) {
            return $q->where('grupo', $request->grupo);
        });

        // Filtro por Cargo (Búsqueda parcial)
        $query->when($request->filled('cargo'), function ($q) use ($request) {
            return $q->where('cargo', 'like', '%' . $request->cargo . '%');
        });

        // Filtro por Estado
        $query->when($request->filled('estado'), function ($q) use ($request) {
            return $q->where('activo', $request->estado);
        });

        // Filtro por Mes (Creación)
        $query->when($request->filled('mes'), function ($q) use ($request) {
            return $q->whereMonth('created_at', $request->mes);
        });

        // Filtro por Año (Creación)
        $query->when($request->filled('anio'), function ($q) use ($request) {
            return $q->whereYear('created_at', $request->anio);
        });
        

        // Ordenar por Grupo y luego por el número de Orden manual
        $miembros = $query->orderBy('grupo')->orderBy('orden')->paginate(10)->appends($request->all());

        return view('gestion-contenido.organizacion-card.index', compact('miembros'));
    }

    public function create()
    {
        return view('gestion-contenido.organizacion-card.create');
    }

    public function store(Request $request)
    {
        // 1. Validamos SIN el campo 'orden'
        $request->validate([
            'nombres' => 'required|string|max:255',
            'cargo'   => 'nullable|string|max:255',
            'grupo'   => 'required|string',
            // 'orden' => 'required', // <--- ELIMINADO
            'imagen'  => 'nullable|image|max:5120',
            'email'   => 'nullable|email',
        ]);

        $ruta = null;
        if ($request->hasFile('imagen')) {
            $ruta = $request->file('imagen')->store('organizacion', 'public');
        }

        // 2. CÁLCULO AUTOMÁTICO DEL ORDEN
        // Buscamos el número más alto actual en ESE grupo específico
        $ultimoOrden = OrganizacionCard::where('grupo', $request->grupo)->max('orden');
        
        // Si existe, le sumamos 1. Si no existe (es el primero), empezamos en 1.
        $nuevoOrden = $ultimoOrden ? ($ultimoOrden + 1) : 1;

        // 3. Crear
        OrganizacionCard::create([
            'nombres'     => $request->nombres,
            'cargo'       => $request->cargo,
            'grupo'       => $request->grupo,
            'orden'       => $nuevoOrden, // <--- Asignamos el valor calculado
            'email'       => $request->email,
            'telefono'    => $request->telefono,
            'ruta_imagen' => $ruta,
            'activo'      => true
        ]);

        return redirect()->route('organizacion-gestion.index')
            ->with('success', 'Miembro registrado correctamente al final de la lista.');
    }

    public function edit($id)
    {
        $miembro = OrganizacionCard::findOrFail($id);
        return view('gestion-contenido.organizacion-card.edit', compact('miembro'));
    }

    public function update(Request $request, $id)
    {
        $miembro = OrganizacionCard::findOrFail($id);

        $request->validate([
            'nombres' => 'required|string',
            // CAMBIO: Ahora es nullable
            'cargo'   => 'nullable|string|max:255', 
            'grupo'   => 'required|string',
           // 'orden'   => 'required|integer',
            'imagen'  => 'nullable|image|max:5120',
        ]);

        $datos = $request->all();

        if ($request->hasFile('imagen')) {
            if ($miembro->ruta_imagen) {
                Storage::disk('public')->delete($miembro->ruta_imagen);
            }
            $datos['ruta_imagen'] = $request->file('imagen')->store('organizacion', 'public');
        }

        $miembro->update($datos);

        return redirect()->route('organizacion-gestion.index')->with('success', 'Datos actualizados.');
    }

    public function destroy($id)
    {
        $miembro = OrganizacionCard::findOrFail($id);
        if ($miembro->ruta_imagen) {
            Storage::disk('public')->delete($miembro->ruta_imagen);
        }
        $miembro->delete();
        return redirect()->route('organizacion-gestion.index')->with('success', 'Miembro eliminado.');
    }
    
    public function toggleEstado($id)
    {
        $miembro = OrganizacionCard::findOrFail($id);
        $miembro->activo = !$miembro->activo;
        $miembro->save();
        return redirect()->route('organizacion-gestion.index')->with('success', 'Estado actualizado.');
    }
}