<?php

namespace App\Http\Controllers;

use App\Models\OrganizacionCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager; // Si usas Intervention V3 (Descomentar si da error la de arriba)
use Intervention\Image\Drivers\Gd\Driver; // Si usas Intervention V3

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
        Log::info('--- INICIO STORE ORGANIZACION ---');
        
            $request->validate([
                'nombres'      => 'required|string|max:255',
                'codigo'       => 'nullable|string|max:50|unique:organizacion_card,codigo',
                'cargo'        => 'nullable|string|max:255',
                'especialidad' => 'nullable|string|max:255', // NUEVO
                'grupo'        => 'required|string',
                'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'cv'           => 'nullable|file|mimes:pdf|max:10240', // NUEVO (PDF hasta 10MB)
                'email'        => 'nullable|email',
            ]);

            try {
            $rutaImagen = null;
            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $imagen = $request->file('imagen');
                
                // 1. Generar nombre único con extensión .webp
                $nombreArchivo = Str::uuid() . '.webp';
                $rutaDestino = 'organizacion/fotos/' . $nombreArchivo;

                $manager = new ImageManager(new Driver());
                $img = $manager->read($imagen)->toWebp(80);

                Storage::disk('public')->put($rutaDestino, $img);
                
                // 4. Asignar la ruta para guardar en BD
                $rutaImagen = $rutaDestino;
            }

            $rutaCv = null;
            if ($request->hasFile('cv') && $request->file('cv')->isValid()) {
                $rutaCv = $request->file('cv')->store('organizacion/cvs', 'public');
            }

            // 3. Cálculo de Orden
            $ultimoOrden = OrganizacionCard::where('grupo', $request->grupo)->max('orden');
            $nuevoOrden = $ultimoOrden ? ($ultimoOrden + 1) : 1;

            // 4. Crear Registro
            OrganizacionCard::create([
                'nombres'      => $request->nombres,
                'codigo'       => $request->codigo,
                'cargo'        => $request->cargo,
                'especialidad' => $request->especialidad, // NUEVO
                'grupo'        => $request->grupo,
                'orden'        => $nuevoOrden,
                'email'        => $request->email,
                'telefono'     => $request->telefono,
                'ruta_imagen'  => $rutaImagen,
                'ruta_cv'      => $rutaCv, // NUEVO
                'activo'       => true
            ]);

            return redirect()->route('organizacion-gestion.index')
                ->with('success', 'Miembro registrado correctamente.');

        } catch (\Exception $e) {
            Log::error('ERROR CRÍTICO en Organizacion Store: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error del sistema: ' . $e->getMessage()]);
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
            'nombres'      => 'required|string|max:255',
            'codigo'       => 'nullable|string|max:50|unique:organizacion_card,codigo,' . $id,
            'cargo'        => 'nullable|string|max:255',
            'especialidad' => 'nullable|string|max:255',
            'grupo'        => 'required|string',
            'imagen'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'cv'           => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $datos = $request->except(['imagen', 'cv']);

        if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
            
            if ($miembro->ruta_imagen && Storage::disk('public')->exists($miembro->ruta_imagen)) {
                Storage::disk('public')->delete($miembro->ruta_imagen);
            }

            $imagen = $request->file('imagen');
            $nombreArchivo = Str::uuid() . '.webp';
            $rutaDestino = 'organizacion/fotos/' . $nombreArchivo;

            $manager = new ImageManager(new Driver());
            $img = $manager->read($imagen)->toWebp(80);

            Storage::disk('public')->put($rutaDestino, $img);

            $datos['ruta_imagen'] = $rutaDestino;
        }

        if ($request->hasFile('cv') && $request->file('cv')->isValid()) {
            
            if ($miembro->ruta_cv && Storage::disk('public')->exists($miembro->ruta_cv)) {
                Storage::disk('public')->delete($miembro->ruta_cv);
            }

            $datos['ruta_cv'] = $request->file('cv')->store('organizacion/cvs', 'public');
        }

        $miembro->update($datos);

        return redirect()->route('organizacion-gestion.index')
            ->with('success', 'Datos actualizados correctamente.');
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

    public function show($id)
    {
        $miembro = OrganizacionCard::findOrFail($id);
        return view('gestion-contenido.organizacion-card.show', compact('miembro'));
    }
}