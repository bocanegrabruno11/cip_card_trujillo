<?php

namespace App\Http\Controllers;

use App\Models\Jrd;
use App\Models\ProcesoJrd;
use App\Models\ProcesoJrdDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class JrdDocumentoController extends Controller
{
    public function store(Request $request, $id_jrd)
    {
        try {
            $request->validate([
                'proceso_id' => 'required|exists:procesos_jrd,id_proceso_jrd', // Nombre correcto de tabla
                'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480'
            ]);

            $jrd = Jrd::find($id_jrd);
            
            if (!$jrd) {
                return back()->with('error', 'JRD no encontrado.');
            }

            $proceso = ProcesoJrd::findOrFail($request->proceso_id);
            
            if (!$proceso) {
                return back()->with('error', 'Proceso no encontrado.');
            }

            if ($proceso->jrd_id != $id_jrd) {
                return back()->with('error', 'El proceso no pertenece a este JRD.');
            }

            $archivo = $request->file('archivo');
            $nombreOriginal = $archivo->getClientOriginalName();
            $extension = strtolower($archivo->getClientOriginalExtension());
            
            $tipoDocumento = ($extension === 'pdf') ? 'pdf' : 'imagen';
            
            // Usa store() simple para evitar problemas
            $ruta = $archivo->store('documentos/jrd', 'public');
            
            // Crea el documento - ATENCIÓN: sin campo 'estado'
            $documento = ProcesoJrdDocumento::create([
                'proceso_jrd_id'   => $proceso->id_proceso_jrd,
                'nombre_original'  => $nombreOriginal,
                'ruta_archivo'     => '/storage/' . $ruta, // Ruta absoluta
                'tipo_documento'   => $tipoDocumento,
                'fecha_subida'     => now(),
            ]);

            Log::info('Documento subido a JRD:', [
                'jrd_id' => $id_jrd,
                'proceso_id' => $proceso->id_proceso_jrd,
                'documento_id' => $documento->id_proceso_jrd_documento,
                'nombre' => $nombreOriginal,
                'ruta' => '/storage/' . $ruta
            ]);

            return back()->with('success', 'Documento subido correctamente.');

        } catch (\Exception $e) {
            Log::error('Error al subir documento a JRD:', [
                'id_jrd' => $id_jrd,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error al subir el documento: ' . $e->getMessage());
        }
    }
}