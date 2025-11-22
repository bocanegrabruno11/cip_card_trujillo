<?php

namespace App\Http\Controllers;

use App\Models\OrganizacionCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        // Filtro por Orden
        $query->when($request->filled('nombres'), function ($q) use ($request) {
            return $q->where('nombres', 'like', '%' . $request->nombres . '%');
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
        // LOG 1: Ver qué llega exactamente
        Log::info('--- INICIO STORE ORGANIZACION ---');
        Log::info('Datos recibidos:', $request->except('imagen'));

        try {
            // 1. Validación
            $request->validate([
                'nombres' => 'required|string|max:255',
                'cargo'   => 'nullable|string|max:255',
                'grupo'   => 'required|string',
                // Validamos la imagen con soporte amplio
                'imagen'  => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120', 
                'email'   => 'nullable|email',
            ]);

            // LOG 2: Validación pasó
            Log::info('Validación exitosa.');

            $ruta = null;
            if ($request->hasFile('imagen')) {
                $archivo = $request->file('imagen');
                
                // Verificar si el archivo es válido antes de guardar
                if ($archivo->isValid()) {
                    $ruta = $archivo->store('organizacion', 'public');
                    Log::info('Imagen guardada en: ' . $ruta);
                } else {
                    Log::error('El archivo de imagen se recibió pero NO es válido (Error PHP: ' . $archivo->getError() . ')');
                }
            } else {
                Log::info('No se recibió archivo de imagen.');
            }

            // 2. Cálculo Automático del Orden
            $ultimoOrden = OrganizacionCard::where('grupo', $request->grupo)->max('orden');
            $nuevoOrden = $ultimoOrden ? ($ultimoOrden + 1) : 1;
            
            Log::info("Orden calculado: {$nuevoOrden} para el grupo: {$request->grupo}");

            // 3. Crear Registro
            $miembro = OrganizacionCard::create([
                'nombres'     => $request->nombres,
                'cargo'       => $request->cargo,
                'grupo'       => $request->grupo,
                'orden'       => $nuevoOrden,
                'email'       => $request->email,
                'telefono'    => $request->telefono,
                'ruta_imagen' => $ruta,
                'activo'      => true
            ]);

            Log::info('Registro creado en BD con ID: ' . $miembro->id);

            return redirect()->route('organizacion-gestion.index')
                ->with('success', 'Miembro registrado correctamente al final de la lista.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si falla la validación, logueamos POR QUÉ falló
            Log::error('Fallo de Validación:', $e->errors());
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            // Cualquier otro error (Base de datos, permisos, etc.)
            Log::error('ERROR CRÍTICO en Organizacion Store: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['error' => 'Error del sistema: ' . $e->getMessage()]);
        }
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
            'imagen'  => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
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