<?php

namespace App\Http\Controllers;

use App\Models\ProcesoJrd;
use App\Models\ProcesoJrdDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JrdDocumentoController extends Controller
{
    public function store(Request $request, $id_jrd)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'proceso_id' => 'required|exists:procesos_jrd,id_proceso_jrd'
        ]);

        // 1️⃣ Obtener el proceso específico
        $proceso = ProcesoJrd::find($request->proceso_id);
        
        if (!$proceso || $proceso->jrd_id != $id_jrd) {
            return back()->with('error', 'Proceso no válido para este JRD.');
        }

        // 2️⃣ Guardar archivo en storage/app/public/uploads/vouchers
        $file = $request->file('archivo');
        
        // Crear nombre único
        $filename = time() . '_' . $file->getClientOriginalName();
        
        // Guardar usando storeAs
        $relativePath = $file->storeAs('uploads/vouchers', $filename, 'public');
        
        // Crear la ruta completa CON /storage/ al inicio
        $fullPath = '/storage/' . $relativePath;

        // 3️⃣ Determinar tipo de documento
        $extension = strtolower($file->getClientOriginalExtension());
        $tipoDocumento = ($extension === 'pdf') ? 'pdf' : 'imagen';

        // 4️⃣ Registrar documento CON la ruta completa que incluye /storage/
        ProcesoJrdDocumento::create([
            'proceso_jrd_id'   => $proceso->id_proceso_jrd,
            'fecha_subida'     => now(),
            'tipo_documento'   => $tipoDocumento,
            'nombre_original'  => $file->getClientOriginalName(),
            'ruta_archivo'     => $fullPath, // "/storage/uploads/vouchers/1765993567_nombre.pdf"
        ]);

        return back()->with('success', 'Documento registrado correctamente.');
    }

    /**
     * OPCIONAL: Método para agregar documento desde Drive
     */
    public function storeDrive(Request $request, $id_jrd)
    {
        $request->validate([
            'drive_link' => 'required|url',
            'nombre_documento' => 'required|string|max:255',
            'proceso_id' => 'required|exists:procesos_jrd,id_proceso_jrd'
        ]);

        $proceso = ProcesoJrd::find($request->proceso_id);
        
        if (!$proceso || $proceso->jrd_id != $id_jrd) {
            return back()->with('error', 'Proceso no válido para este JRD.');
        }

        ProcesoJrdDocumento::create([
            'proceso_jrd_id'   => $proceso->id_proceso_jrd,
            'fecha_subida'     => now(),
            'tipo_documento'   => 'otro',
            'nombre_original'  => $request->nombre_documento,
            'ruta_archivo'     => $request->drive_link,
        ]);

        return back()->with('success', 'Enlace de Drive registrado correctamente.');
    }

    /**
     * OPCIONAL: Método para eliminar documento
     */
    public function destroy($id_jrd, $id_documento)
    {
        try {
            $documento = ProcesoJrdDocumento::findOrFail($id_documento);
            
            // Verificar que el documento pertenezca al JRD
            $proceso = $documento->proceso;
            if ($proceso->jrd_id != $id_jrd) {
                return back()->with('error', 'Documento no pertenece a este JRD.');
            }

            // Si es un archivo local (no enlace de Drive), eliminarlo del almacenamiento
            if (!filter_var($documento->ruta_archivo, FILTER_VALIDATE_URL) && 
                strpos($documento->ruta_archivo, '/storage/') === 0) {
                $relativePath = str_replace('/storage/', '', $documento->ruta_archivo);
                Storage::disk('public')->delete($relativePath);
            }

            $documento->delete();

            return back()->with('success', 'Documento eliminado correctamente.');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el documento.');
        }
    }
}