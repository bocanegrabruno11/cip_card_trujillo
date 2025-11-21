<?php

namespace App\Http\Controllers;

use App\Models\DetallePublicacion;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PublicacionController extends Controller
{
    // 1. Listar Publicaciones
    public function index(Request $request)
    {
        // Iniciamos la consulta base
        $query = Publicacion::with('detalles');

        // 1. Filtro por Sección
        $query->when($request->filled('seccion'), function ($q) use ($request) {
            return $q->where('seccion', $request->seccion);
        });

        // 2. Filtro por Estado (Activo / Inactivo)
        // Usamos 'filled' y comprobamos que no sea "todos"
        $query->when($request->filled('estado'), function ($q) use ($request) {
            // Si es '1' busca true, si es '0' busca false
            return $q->where('activo', $request->estado);
        });

        // 3. Filtro por Mes
        $query->when($request->filled('mes'), function ($q) use ($request) {
            return $q->whereMonth('created_at', $request->mes);
        });

        // 4. Filtro por Año
        $query->when($request->filled('anio'), function ($q) use ($request) {
            return $q->whereYear('created_at', $request->anio);
        });

        // Ejecutamos paginación manteniendo los filtros en la URL (appends)
        $publicaciones = $query->latest()->paginate(10)->appends($request->all());

        return view('gestion-contenido.publicaciones.index', compact('publicaciones'));
    }

    public function create()
    {
        return view('gestion-contenido.publicaciones.create');
    }

    public function store(Request $request)
    {
        try {
            // 1. Validación (CORREGIDA PARA AVIF)
            $request->validate([
                'titulo' => 'required|string|max:255',
                'seccion' => 'required|string',
                
                // CAMBIO AQUÍ: Usamos 'file' en vez de 'image' y agregamos 'avif'
                'imagen_principal' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
                
                'descripcion' => 'nullable|string',
                'url_enlace_principal' => 'nullable|url',
                'galeria' => 'nullable|array',
                
                // CAMBIO AQUÍ TAMBIÉN:
                'galeria.*.imagen' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,avif|max:10240',
                
                'galeria.*.url_enlace' => 'nullable|url',
                'galeria.*.descripcion' => 'nullable|string',
            ]);

            DB::transaction(function () use ($request) {
                
                // A. Crear Publicación Padre
                $publicacion = Publicacion::create([
                    'titulo' => $request->titulo,
                    'descripcion' => $request->descripcion,
                    'seccion' => $request->seccion,
                    'activo' => true
                ]);

                // B. Guardar Imagen Principal
                if ($request->hasFile('imagen_principal')) {
                    $archivo = $request->file('imagen_principal');
                    // Obtenemos extensión segura del archivo
                    $extension = $archivo->getClientOriginalExtension(); 
                    $nombre = "main_" . time() . '.' . $extension;
                    
                    $ruta = $archivo->storeAs('publicaciones/' . $publicacion->id, $nombre, 'public');

                    DetallePublicacion::create([
                        'publicacion_id' => $publicacion->id,
                        'ruta_imagen' => $ruta,
                        'url_enlace' => $request->url_enlace_principal,
                        'grupo' => 'principal'
                    ]);
                }

                // C. Guardar Galería
                if ($request->has('galeria') && is_array($request->galeria)) {
                    
                    foreach ($request->galeria as $index => $item) {
                        
                        $keyFile = "galeria.{$index}.imagen";

                        if ($request->hasFile($keyFile)) {
                            $archivo = $request->file($keyFile);
                            $extension = $archivo->getClientOriginalExtension();
                            $nombre = "slider_{$index}_" . time() . '.' . $extension;
                            
                            $ruta = $archivo->storeAs('publicaciones/' . $publicacion->id, $nombre, 'public');

                            DetallePublicacion::create([
                                'publicacion_id' => $publicacion->id,
                                'ruta_imagen' => $ruta,
                                'url_enlace' => $item['url_enlace'] ?? null,
                                'descripcion' => $item['descripcion'] ?? null,
                                'grupo' => 'galeria'
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('publicaciones.index')
                ->with('success', 'Publicación registrada exitosamente.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retorna error de validación a la vista
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error crítico en Store: ' . $e->getMessage());
            return back()->withInput()
                ->withErrors(['error' => 'Error del sistema: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        // Cargamos la publicación con sus detalles separados por grupo
        $publicacion = Publicacion::with('detalles')->findOrFail($id);
        
        $imagenPrincipal = $publicacion->detalles->where('grupo', 'principal')->first();
        $galeria = $publicacion->detalles->where('grupo', 'galeria');

        return view('gestion-contenido.publicaciones.edit', compact('publicacion', 'imagenPrincipal', 'galeria'));
    }

    public function update(Request $request, $id)
    {
        $publicacion = Publicacion::findOrFail($id);

        try {
            // Validación
            $request->validate([
                'url_enlace_principal' => 'nullable|url',
                // Validar arrays de edición
                'galeria_existente.*.url_enlace' => 'nullable|url',
                'galeria_existente.*.descripcion' => 'nullable|string',
            ]);

            DB::transaction(function () use ($request, $publicacion) {
                
                // 1. Actualizar Maestro
                $publicacion->update([
                    'titulo' => $request->titulo,
                    'descripcion' => $request->descripcion,
                    'seccion' => $request->seccion,
                ]);

                // 2. Manejo de Imagen Principal
                $detallePrincipal = $publicacion->detalles->where('grupo', 'principal')->first();

                if ($request->hasFile('imagen_principal')) {
                    // CASO A: Subieron una nueva imagen -> Reemplazamos
                    $archivo = $request->file('imagen_principal');
                    
                    if ($archivo->isValid()) {
                        if ($detallePrincipal) {
                            Storage::disk('public')->delete($detallePrincipal->ruta_imagen);
                            $detallePrincipal->delete();
                        }
                        
                        $nombre = "main_" . time() . '.' . $archivo->getClientOriginalExtension();
                        $ruta = $archivo->storeAs('publicaciones/' . $publicacion->id, $nombre, 'public');

                        DetallePublicacion::create([
                            'publicacion_id' => $publicacion->id,
                            'ruta_imagen' => $ruta,
                            'url_enlace' => $request->url_enlace_principal,
                            'grupo' => 'principal'
                        ]);
                    }
                } else {
                    // CASO B: Solo actualizar URL de la principal
                    if ($detallePrincipal) {
                        $detallePrincipal->update([
                            'url_enlace' => $request->url_enlace_principal
                        ]);
                    }
                }

                // 3. Agregar NUEVAS imágenes (Igual que antes)
                if ($request->has('galeria_nueva') && is_array($request->galeria_nueva)) {
                    foreach ($request->galeria_nueva as $index => $item) {
                        $fileKey = "galeria_nueva.{$index}.imagen";
                        if ($request->hasFile($fileKey)) {
                            $archivo = $request->file($fileKey);
                            if ($archivo->isValid()) {
                                $nombre = "slider_{$index}_" . time() . '.' . $archivo->getClientOriginalExtension();
                                $ruta = $archivo->storeAs('publicaciones/' . $publicacion->id, $nombre, 'public');

                                DetallePublicacion::create([
                                    'publicacion_id' => $publicacion->id,
                                    'ruta_imagen' => $ruta,
                                    'url_enlace' => $item['url_enlace'] ?? null,
                                    'descripcion' => $item['descripcion'] ?? null,
                                    'grupo' => 'galeria'
                                ]);
                            }
                        }
                    }
                }

                // 4. ACTUALIZAR DATOS DE GALERÍA EXISTENTE (NUEVO)
                if ($request->has('galeria_existente') && is_array($request->galeria_existente)) {
                    foreach ($request->galeria_existente as $detalleId => $datos) {
                        $detalle = DetallePublicacion::find($detalleId);
                        if ($detalle && $detalle->publicacion_id == $publicacion->id) {
                            $detalle->update([
                                'url_enlace' => $datos['url_enlace'] ?? null,
                                'descripcion' => $datos['descripcion'] ?? null,
                            ]);
                        }
                    }
                }

                // 5. Eliminar imágenes marcadas
                if ($request->has('eliminar_detalles')) {
                    $idsBorrar = $request->eliminar_detalles;
                    $detallesBorrar = DetallePublicacion::whereIn('id', $idsBorrar)->get();
                    foreach ($detallesBorrar as $detalle) {
                        Storage::disk('public')->delete($detalle->ruta_imagen);
                        $detalle->delete();
                    }
                }
            });

            return redirect()->route('publicaciones.index')->with('success', 'Publicación actualizada correctamente.');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error Update: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    // 6. Eliminar
    public function destroy($id)
    {
        $publicacion = Publicacion::findOrFail($id);
        
        // Borrar archivo físico
        if ($publicacion->ruta_imagen) {
            Storage::disk('public')->delete($publicacion->ruta_imagen);
        }
        
        $publicacion->delete();

        return redirect()->route('publicaciones.index')
            ->with('success', 'Publicación eliminada.');
    }

    // Método para cambiar el estado (Toggle)
    public function toggleEstado($id)
    {
        $publicacion = Publicacion::findOrFail($id);

        // Lógica inteligente: Si voy a ACTIVAR esta publicación...
        if (!$publicacion->activo) {
            
            // Desactivar todas las demás de la misma sección para evitar duplicados visuales
            // (Especialmente útil para Sliders y Popups donde solo quieres ver uno a la vez)
            Publicacion::where('seccion', $publicacion->seccion)
                ->where('id', '!=', $id)
                ->update(['activo' => false]);
        }

        // Invertir el estado actual
        $publicacion->activo = !$publicacion->activo;
        $publicacion->save();

        $estado = $publicacion->activo ? 'Activada' : 'Desactivada';

        return redirect()->back()->with('success', "Publicación {$estado} correctamente.");
    }
}
