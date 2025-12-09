<?php

namespace App\Http\Controllers;

use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager; // Si usas Intervention V3 (Descomentar si da error la de arriba)
use Intervention\Image\Drivers\Gd\Driver; // Si usas Intervention V3
class ComunicadoController extends Controller
{
    // 1. LISTAR Y FILTRAR
    public function index(Request $request)
    {
        $query = Comunicado::query();

        // Filtro por Estado
        $query->when($request->filled('estado'), function ($q) use ($request) {
            return $q->where('activo', $request->estado);
        });

        // Filtro por Año
        $query->when($request->filled('anio'), function ($q) use ($request) {
            return $q->whereYear('created_at', $request->anio);
        });

        // Filtro por Mes
        $query->when($request->filled('mes'), function ($q) use ($request) {
            return $q->whereMonth('created_at', $request->mes);
        });

        $comunicados = $query->latest()->paginate(10)->appends($request->all());

        return view('gestion-contenido.comunicados.index', compact('comunicados'));
    }

    // 2. VISTA CREAR
    public function create()
    {
        return view('gestion-contenido.comunicados.create');
    }

    // 3. GUARDAR
    public function store(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'url_enlace' => 'nullable|url',
                'imagen' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
            ]);

            $ruta = null;

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $archivo = $request->file('imagen');
                
                // 1. Generar nombre único .webp
                $nombre = Str::uuid() . '.webp';
                $ruta = 'comunicados/' . $nombre;

                // 2. Convertir y Guardar (WebP calidad 80)
                $manager = new ImageManager(new Driver());
                $img = $manager->read($archivo)->toWebp(80);
                
                Storage::disk('public')->put($ruta, $img);
            }

            Comunicado::create([
                'titulo' => $request->titulo,
                'descripcion' => $request->descripcion,
                'url_enlace' => $request->url_enlace,
                'ruta_imagen' => $ruta,
                'activo' => true,
            ]);

            return redirect()->route('comunicados.index')->with('success', 'Comunicado registrado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error Store Comunicado: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    // 4. VISTA EDITAR
    public function edit($id)
    {
        $comunicado = Comunicado::findOrFail($id);
        return view('gestion-contenido.comunicados.edit', compact('comunicado'));
    }

    public function update(Request $request, $id)
    {
        $comunicado = Comunicado::findOrFail($id);

        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'url_enlace' => 'nullable|url',
                'imagen' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
            ]);

            $datos = $request->only(['titulo', 'descripcion', 'url_enlace']);

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                
                if ($comunicado->ruta_imagen && Storage::disk('public')->exists($comunicado->ruta_imagen)) {
                    Storage::disk('public')->delete($comunicado->ruta_imagen);
                }

                $archivo = $request->file('imagen');
                $nombre = Str::uuid() . '.webp';
                $ruta = 'comunicados/' . $nombre;

                $manager = new ImageManager(new Driver());
                $img = $manager->read($archivo)->toWebp(80);
                
                Storage::disk('public')->put($ruta, $img);

                $datos['ruta_imagen'] = $ruta;
            }

            $comunicado->update($datos);

            return redirect()->route('comunicados.index')->with('success', 'Comunicado actualizado.');

        } catch (\Exception $e) {
            Log::error('Error Update Comunicado: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    // 6. ELIMINAR
    public function destroy($id)
    {
        $comunicado = Comunicado::findOrFail($id);
        
        if ($comunicado->ruta_imagen) {
            Storage::disk('public')->delete($comunicado->ruta_imagen);
        }
        
        $comunicado->delete();
        return redirect()->route('comunicados.index')->with('success', 'Comunicado eliminado.');
    }

    // 7. CAMBIAR ESTADO (Activo/Inactivo)
    public function toggleEstado($id)
    {
        $comunicado = Comunicado::findOrFail($id);
        $comunicado->activo = !$comunicado->activo;
        $comunicado->save();
        
        return redirect()->back()->with('success', 'Estado actualizado.');
    }

    public function show($id)
    {
        $comunicado = Comunicado::findOrFail($id);
        return view('gestion-contenido.comunicados.show', compact('comunicado'));
    }
}