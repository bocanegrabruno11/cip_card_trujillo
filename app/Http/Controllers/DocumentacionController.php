<?php

namespace App\Http\Controllers;

use App\Models\Documentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager; // Si usas Intervention V3 (Descomentar si da error la de arriba)
use Intervention\Image\Drivers\Gd\Driver; // Si usas Intervention V3
class DocumentacionController extends Controller
{
    public function index(Request $request)
    {
        // Query base
        $query = Documentacion::query();

        // 1. Filtro por Sección
        if ($request->filled('seccion')) {
            $query->where('seccion', $request->seccion);
        }

        // 2. Filtro por Categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        // 3. Filtro por Mes (de Fecha Publicación)
        if ($request->filled('mes')) {
            $query->whereMonth('fecha_publicacion', $request->mes);
        }

        // 4. Filtro por Año
        if ($request->filled('anio')) {
            $query->whereYear('fecha_publicacion', $request->anio);
        }
        
        // 5. Búsqueda por Título
        if ($request->filled('search')) {
            $query->where('titulo', 'like', '%' . $request->search . '%');
        }

        // Ordenar por fecha de publicación descendente (lo más nuevo primero)
        $documentos = $query->orderBy('fecha_publicacion', 'desc')->paginate(10);

        return view('gestion-contenido.documentacion.index', compact('documentos'));
    }

    public function create()
    {
        return view('gestion-contenido.documentacion.create');
    }

    private function processAndStoreFile($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Verificamos si es una imagen compatible para conversión
        if (in_array($extension, $imageExtensions)) {
            try {
                // 1. Iniciar el manejador de imágenes con driver GD
                $manager = new ImageManager(new Driver());

                // 2. Leer el archivo subido
                $image = $manager->read($file->getPathname());

                // 3. Convertir a WebP con calidad 80% (ajustable)
                $encoded = $image->toWebp(80);

                // 4. Generar nombre único y ruta
                $filename = Str::uuid() . '.webp';
                $path = 'documentacion/' . $filename;

                // 5. Guardar la imagen convertida en el disco 'public'
                Storage::disk('public')->put($path, $encoded);

                return $path; // Retornamos la nueva ruta WebP

            } catch (\Exception $e) {
                Log::error("Error convirtiendo imagen a WebP, guardando original: " . $e->getMessage());
                // Fallback: Si falla la conversión, guardar el archivo original normalmente
                return $file->store('documentacion', 'public');
            }
        } 
        else {
            // NO es imagen (PDF, DOCX, XLSX...), guardar normalmente
            return $file->store('documentacion', 'public');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_publicacion' => 'required|date',
            'seccion' => 'required|string',
            // Validamos que acepte documentos E imágenes
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:20480', // Aumenté a 20MB por si acaso
        ]);

        $data = $request->except('archivo');
        unset($data['ruta_miniatura']); // Aseguramos que este campo no moleste

        // Manejo del Archivo usando la función auxiliar
        if ($request->hasFile('archivo')) {
            $data['ruta_archivo'] = $this->processAndStoreFile($request->file('archivo'));
        }

        Documentacion::create($data);

        return redirect()->route('documentos-gestion.index')->with('success', 'Documento registrado correctamente.');
    }

    public function edit($id)
    {
        $documento = Documentacion::findOrFail($id);
        return view('gestion-contenido.documentacion.edit', compact('documento'));
    }

    public function update(Request $request, $id)
    {
        $documento = Documentacion::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_publicacion' => 'required|date',
            'seccion' => 'required|string',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,webp|max:20480',
        ]);

        $data = $request->except('archivo');
        unset($data['ruta_miniatura']);

        // Si suben un nuevo archivo
        if ($request->hasFile('archivo')) {
            // 1. Borrar el archivo anterior del storage
            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }
            
            // 2. Procesar y guardar el nuevo archivo (convirtiendo a WebP si es imagen)
            $data['ruta_archivo'] = $this->processAndStoreFile($request->file('archivo'));
        }

        $documento->update($data);

        return redirect()->route('documentos-gestion.index')->with('success', 'Documento actualizado correctamente.');
    }

    public function toggleEstado($id)
    {
        $doc = Documentacion::findOrFail($id);
        $doc->activo = !$doc->activo;
        $doc->save();

        return back()->with('success', 'Estado de visibilidad actualizado.');
    }

    public function destroy($id)
    {
        $doc = Documentacion::findOrFail($id);
        
        // Eliminar archivo físico
        if ($doc->ruta_archivo && Storage::disk('public')->exists($doc->ruta_archivo)) {
            Storage::disk('public')->delete($doc->ruta_archivo);
        }

        $doc->delete();
        return back()->with('success', 'Documento eliminado correctamente.');
    }
}