<?php

namespace App\Http\Controllers;

use App\Models\Jrd;
use App\Models\ProcesoJrd;
use App\Models\ProcesoJrdDocumento;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class JrdDocumentoController extends Controller
{
    /**
     * Store documento para Admin (JSON response)
     */
    public function store(Request $request, $id_jrd)
    {
        try {
            $request->validate([
                'proceso_id'      => 'required|exists:procesos_jrd,id_proceso_jrd',
                'tipo_documento'  => 'required|in:archivo,link',
                'nombre_documento'=> 'required|string|max:255',
                'observaciones'   => 'nullable|string|max:500',
            ]);

            if ($request->tipo_documento === 'archivo') {
                $request->validate([
                    'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480'
                ]);
            } else {
                $request->validate([
                    'link' => 'required|url|max:500'
                ]);
            }

            $jrd = Jrd::find($id_jrd);

            if (!$jrd) {
                return response()->json([
                    'success' => false,
                    'message' => 'JRD no encontrado.'
                ], 404);
            }

            if ($jrd->estado === 'archivado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este JRD está archivado. No se pueden subir documentos.'
                ], 400);
            }

            $proceso = ProcesoJrd::where('id_proceso_jrd', $request->proceso_id)
                ->where('jrd_id', $id_jrd)
                ->first();

            if (!$proceso) {
                return response()->json([
                    'success' => false,
                    'message' => 'El proceso no pertenece a este JRD.'
                ], 400);
            }

            // DESPUÉS:
            if (!in_array($proceso->estado, ['activo', 'observado'])) {
                return response()->json(['success' => false, 'message' => 'Este proceso no permite subir documentos.'], 400);
            }

            $rutaArchivo = null;
            $tipoDoc     = 'otro';

            if ($request->tipo_documento === 'archivo') {
                $archivo   = $request->file('archivo');
                $filename  = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $ruta      = $archivo->storeAs('documentos/jrd', $filename, 'public');
                $rutaArchivo = '/storage/' . $ruta;
                $extension = strtolower($archivo->getClientOriginalExtension());
                $tipoDoc   = ($extension === 'pdf') ? 'pdf' : 'imagen';
            } else {
                $rutaArchivo = $request->link;
                $tipoDoc     = 'otro';
            }

            $documento = ProcesoJrdDocumento::create([
                'proceso_jrd_id'  => $proceso->id_proceso_jrd,
                'fecha_subida'    => now(),
                'tipo_documento'  => $tipoDoc,
                'nombre_original' => $request->nombre_documento,
                'ruta_archivo'    => $rutaArchivo,
                'observaciones'   => $request->observaciones ?? null,
                'user_id'         => Auth::id()
            ]);
           if ($proceso->estado === 'observado') {
                $proceso->estado = 'activo';
                $proceso->save();

                $jrd->estado = 'en proceso';
                $jrd->save();
            }
            Log::info('Documento subido a JRD por admin:', [
                'jrd_id'       => $id_jrd,
                'proceso_id'   => $proceso->id_proceso_jrd,
                'documento_id' => $documento->id_proceso_jrd_documento,
            ]);
            NotificacionService::notificarInvolucrados(
                $jrd, 
                'jrd', 
                'Nuevo Documento Adjuntado', 
                "Se ha adjuntado un nuevo documento al expediente JRD: {$request->nombre_documento}. Ingrese al sistema para revisarlo."
            );

            return response()->json([
                'success'   => true,
                'message'   => 'Documento subido correctamente.',
                'documento' => $documento
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al subir documento a JRD:', [
                'id_jrd'  => $id_jrd,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al subir el documento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store documento para Mesa de Partes (JSON response)
     */
    public function storeMesaPartes(Request $request, $id_jrd)
    {
        try {
            $request->validate([
                'proceso_id'      => 'required|exists:procesos_jrd,id_proceso_jrd',
                'tipo_documento'  => 'required|in:archivo,link',
                'nombre_documento'=> 'required|string|max:255',
            ]);

            if ($request->tipo_documento === 'archivo') {
                $request->validate([
                    'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480'
                ]);
            } else {
                $request->validate([
                    'link' => 'required|url|max:500'
                ]);
            }

            $jrd = Jrd::find($id_jrd);

            if (!$jrd) {
                return response()->json(['success' => false, 'message' => 'JPRD no encontrado'], 404);
            }

            $proceso = ProcesoJrd::where('id_proceso_jrd', $request->proceso_id)
                ->where('jrd_id', $id_jrd)
                ->first();

            if (!$proceso) {
                return response()->json(['success' => false, 'message' => 'Proceso no válido para este JPRD'], 400);
            }

            // DESPUÉS:
            if (!in_array($proceso->estado, ['activo', 'observado'])) {
                return response()->json(['success' => false, 'message' => 'Este proceso no permite subir documentos.'], 400);
            }

            $rutaArchivo = null;
            $tipoDoc     = 'otro';

            if ($request->tipo_documento === 'archivo') {
                $archivo     = $request->file('archivo');
                $filename    = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $archivo->getClientOriginalName());
                $ruta        = $archivo->storeAs('documentos/jrd', $filename, 'public');
                $rutaArchivo = '/storage/' . $ruta;
                $extension   = strtolower($archivo->getClientOriginalExtension());
                $tipoDoc     = ($extension === 'pdf') ? 'pdf' : 'imagen';
            } else {
                $rutaArchivo = $request->link;
                $tipoDoc     = 'otro';
            }

            $documento = ProcesoJrdDocumento::create([
                'proceso_jrd_id'  => $proceso->id_proceso_jrd,
                'fecha_subida'    => now(),
                'tipo_documento'  => $tipoDoc,
                'nombre_original' => $request->nombre_documento,
                'ruta_archivo'    => $rutaArchivo,
                'user_id'         => Auth::id(),
                'observaciones'   => $request->observaciones ?? null
            ]);
             if ($proceso->estado === 'observado') {
                    $proceso->estado = 'activo';
                    $proceso->save();
                    $jrd->estado = 'en proceso';
                    $jrd->save();
                }
                NotificacionService::notificarInvolucrados(
                    $jrd, 
                    'jrd', 
                    'Nuevo Documento Adjuntado', 
                    "Una de las partes involucradas ha adjuntado un nuevo documento al expediente JRD: {$request->nombre_documento}."
                );
            return response()->json([
                'success'   => true,
                'message'   => 'Documento subido correctamente',
                'documento' => $documento
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error al subir documento JRD (mesa partes):', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }
}