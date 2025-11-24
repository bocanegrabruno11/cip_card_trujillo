<?php

namespace App\Http\Controllers;

use App\Models\Documentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'fecha_publicacion' => 'required|date',
            'seccion' => 'required|string',
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // Max 10MB
        ]);

        $data = $request->except('archivo');

        // Manejo del Archivo
        if ($request->hasFile('archivo')) {
            // Guardar en storage/app/public/documentacion/
            $path = $request->file('archivo')->store('documentacion', 'public');
            $data['ruta_archivo'] = $path;
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
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        $data = $request->except('archivo');

        // Si suben un nuevo archivo, borramos el anterior y guardamos el nuevo
        if ($request->hasFile('archivo')) {
            if ($documento->ruta_archivo && Storage::disk('public')->exists($documento->ruta_archivo)) {
                Storage::disk('public')->delete($documento->ruta_archivo);
            }
            $path = $request->file('archivo')->store('documentacion', 'public');
            $data['ruta_archivo'] = $path;
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