<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\DetalleEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventoController extends Controller
{
    // 1. INDEX CON FILTROS
    public function index(Request $request)
    {
        $query = Evento::with('detalles');

        // Filtro Estado
        $query->when($request->filled('estado'), function ($q) use ($request) {
            return $q->where('activo', $request->estado);
        });

        // Filtro Mes (Basado en la fecha del evento)
        $query->when($request->filled('mes'), function ($q) use ($request) {
            return $q->whereMonth('fecha_evento', $request->mes);
        });

        // Filtro Año (Basado en la fecha del evento)
        $query->when($request->filled('anio'), function ($q) use ($request) {
            return $q->whereYear('fecha_evento', $request->anio);
        });

        $eventos = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

        return view('gestion-contenido.eventos.index', compact('eventos'));
    }

    // 2. CREATE
    public function create()
    {
        return view('gestion-contenido.eventos.create');
    }

    // 3. STORE
    public function store(Request $request)
    {
        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'fecha_evento' => 'required|date',
                'lugar' => 'nullable|string|max:255',
                'descripcion' => 'nullable|string',
                'imagen_principal' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120', // Obligatoria
                'galeria' => 'nullable|array',
                'galeria.*' => 'file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120'
            ]);

            DB::transaction(function () use ($request) {
                // A. Crear Evento
                $evento = Evento::create([
                    'titulo' => $request->titulo,
                    'fecha_evento' => $request->fecha_evento,
                    'lugar' => $request->lugar,
                    'descripcion' => $request->descripcion,
                    'activo' => true
                ]);

                // B. Guardar Imagen Principal
                if ($request->hasFile('imagen_principal')) {
                    $archivo = $request->file('imagen_principal');
                    $nombre = "main_" . time() . '.' . $archivo->getClientOriginalExtension();
                    $ruta = $archivo->storeAs('eventos/' . $evento->id, $nombre, 'public');

                    DetalleEvento::create([
                        'evento_id' => $evento->id,
                        'ruta_imagen' => $ruta,
                        'tipo' => 'principal'
                    ]);
                }

                // C. Guardar Galería
                if ($request->has('galeria')) {
                    foreach ($request->galeria as $index => $archivo) {
                        if ($archivo->isValid()) {
                            $nombre = "galeria_{$index}_" . time() . '.' . $archivo->getClientOriginalExtension();
                            $ruta = $archivo->storeAs('eventos/' . $evento->id, $nombre, 'public');

                            DetalleEvento::create([
                                'evento_id' => $evento->id,
                                'ruta_imagen' => $ruta,
                                'tipo' => 'galeria'
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('eventos.index')->with('success', 'Evento registrado correctamente.');

        } catch (\Exception $e) {
            Log::error('Error Store Evento: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    // 4. EDIT
    public function edit($id)
    {
        $evento = Evento::with('detalles')->findOrFail($id);
        $imagenPrincipal = $evento->detalles->where('tipo', 'principal')->first();
        $galeria = $evento->detalles->where('tipo', 'galeria');

        return view('gestion-contenido.eventos.edit', compact('evento', 'imagenPrincipal', 'galeria'));
    }

    // 5. UPDATE
    public function update(Request $request, $id)
    {
        $evento = Evento::findOrFail($id);

        try {
            $request->validate([
                'titulo' => 'required|string|max:255',
                'fecha_evento' => 'required|date',
                'imagen_principal' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:5120',
            ]);

            DB::transaction(function () use ($request, $evento) {
                // A. Actualizar Datos
                $evento->update($request->only(['titulo', 'fecha_evento', 'lugar', 'descripcion']));

                // B. Actualizar Principal
                if ($request->hasFile('imagen_principal')) {
                    // Borrar anterior
                    $old = $evento->detalles->where('tipo', 'principal')->first();
                    if ($old) {
                        Storage::disk('public')->delete($old->ruta_imagen);
                        $old->delete();
                    }
                    // Subir nueva
                    $archivo = $request->file('imagen_principal');
                    $nombre = "main_" . time() . '.' . $archivo->getClientOriginalExtension();
                    $ruta = $archivo->storeAs('eventos/' . $evento->id, $nombre, 'public');

                    DetalleEvento::create([
                        'evento_id' => $evento->id,
                        'ruta_imagen' => $ruta,
                        'tipo' => 'principal'
                    ]);
                }

                // C. Nuevas Imágenes Galería
                if ($request->has('galeria_nueva')) {
                    foreach ($request->galeria_nueva as $index => $archivo) {
                        if ($archivo->isValid()) {
                            $nombre = "galeria_new_{$index}_" . time() . '.' . $archivo->getClientOriginalExtension();
                            $ruta = $archivo->storeAs('eventos/' . $evento->id, $nombre, 'public');

                            DetalleEvento::create([
                                'evento_id' => $evento->id,
                                'ruta_imagen' => $ruta,
                                'tipo' => 'galeria'
                            ]);
                        }
                    }
                }

                // D. Eliminar seleccionadas
                if ($request->has('eliminar_detalles')) {
                    $ids = $request->eliminar_detalles;
                    $borrar = DetalleEvento::whereIn('id', $ids)->get();
                    foreach ($borrar as $d) {
                        Storage::disk('public')->delete($d->ruta_imagen);
                        $d->delete();
                    }
                }
            });

            return redirect()->route('eventos.index')->with('success', 'Evento actualizado.');

        } catch (\Exception $e) {
            Log::error('Error Update Evento: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // 6. DESTROY
    public function destroy($id)
    {
        $evento = Evento::with('detalles')->findOrFail($id);
        // Borrar carpeta completa del evento
        Storage::disk('public')->deleteDirectory('eventos/' . $evento->id);
        $evento->delete();

        return redirect()->route('eventos.index')->with('success', 'Evento eliminado.');
    }

    // 7. TOGGLE ESTADO
    public function toggleEstado($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->activo = !$evento->activo;
        $evento->save();
        return redirect()->back()->with('success', 'Estado actualizado.');
    }
}
