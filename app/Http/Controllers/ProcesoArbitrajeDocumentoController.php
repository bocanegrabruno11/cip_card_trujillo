<?php

namespace App\Http\Controllers;

use App\Models\ProcesoDeArbitraje;
use App\Models\ProcesoArbitrajeDocumento;
use App\Models\Arbitraje;
use App\Services\NotificacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcesoArbitrajeDocumentoController extends Controller
{
    public function store(Request $request, $id_arbitraje)
    {
        try {
            Log::info('Iniciando subida de documento', [
                'id_arbitraje' => $id_arbitraje,
                'user_id'      => Auth::id(),
                'request_data' => $request->except(['archivo'])
            ]);

            // ── Validación principal ──────────────────────────────────────────
            $request->validate([
                'proceso_id'      => 'required|exists:proceso_de_arbitraje,id_proceso_de_arbitraje',
                'tipo_documento'  => 'required|in:archivo,link,voucher',
                'nombre_documento'=> 'required|string|max:255',
                'observaciones'   => 'nullable|string'
            ]);

            // ── Validación según tipo ─────────────────────────────────────────
            if (in_array($request->tipo_documento, ['archivo', 'voucher'])) {
                $request->validate([
                    'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480'
                ]);
            } else {
                $request->validate([
                    'link' => 'required|url|max:500'
                ]);
            }

            // ── Verificar que el proceso pertenece al arbitraje ───────────────
            $proceso = ProcesoDeArbitraje::where('id_proceso_de_arbitraje', $request->proceso_id)
                ->where('id_arbitraje', $id_arbitraje)
                ->first();

            if (!$proceso) {
                return response()->json([
                    'success' => false,
                    'message' => 'Proceso no válido para este arbitraje'
                ], 400);
            }

            // ── Verificar que el proceso está activo ──────────────────────────
            if ($proceso->estado !== 'iniciado') {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden subir documentos a procesos activos'
                ], 400);
            }

            // ── Cargar el arbitraje para verificar su estado ──────────────────
            $arbitraje = Arbitraje::find($id_arbitraje);

            // ══════════════════════════════════════════════════════════════════
            // LÓGICA DE RE-SUBIDA DE VOUCHER RECHAZADO
            // Si el arbitraje está en estado 'observado' y se sube un voucher,
            // cambiamos el estado de vuelta a 'validando' automáticamente.
            // ══════════════════════════════════════════════════════════════════
            $esResubirVoucher = ($request->tipo_documento === 'voucher')
                             && $arbitraje
                             && $arbitraje->estado === 'observado';

            $rutaArchivo = null;
            $tipoDoc     = null;

            // ── Manejo de archivos ────────────────────────────────────────────
            if (in_array($request->tipo_documento, ['archivo', 'voucher'])) {
                $file      = $request->file('archivo');
                $filename  = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $carpeta   = $request->tipo_documento === 'voucher' ? 'uploads/vouchers' : 'uploads/documentos';
                $rutaRelativa = $file->storeAs($carpeta, $filename, 'public');
                $rutaArchivo  = '/storage/' . $rutaRelativa;

                $extension = strtolower($file->getClientOriginalExtension());
                $tipoDoc   = $request->tipo_documento === 'voucher' ? 'voucher'
                           : (($extension === 'pdf') ? 'pdf' : 'imagen');

                Log::info('Archivo subido', ['path' => $rutaArchivo, 'tipo' => $tipoDoc]);
            } else {
                $rutaArchivo = $request->link;
                $tipoDoc     = 'otro';
                Log::info('Link guardado', ['link' => $rutaArchivo]);
            }

            DB::beginTransaction();

            // ── Guardar en BD ─────────────────────────────────────────────────
            $documento = ProcesoArbitrajeDocumento::create([
                'id_proceso_de_arbitraje' => $proceso->id_proceso_de_arbitraje,
                'fecha_subida'            => now(),
                'tipo_documento'          => $tipoDoc,
                'nombre_original'         => $request->nombre_documento,
                'ruta_archivo'            => $rutaArchivo,
                'user_id'                 => Auth::id(),
                'observaciones'           => $request->observaciones
            ]);

            // ── Si es re-subida de voucher rechazado: volver a 'validando' ────
            if ($esResubirVoucher) {
                $arbitraje->update(['estado' => 'validando']);

                Log::info('Voucher re-subido: arbitraje vuelto a estado validando', [
                    'id_arbitraje' => $id_arbitraje,
                    'documento_id' => $documento->id_proceso_arbitraje_documento
                ]);
            }
            $asuntoNoti = $esResubirVoucher ? 'Voucher Re-subido' : 'Nuevo Documento Adjuntado';
            $textoNoti = $esResubirVoucher 
                ? "Se ha vuelto a subir el voucher de pago del expediente. El caso ha retornado al estado de Validación."
                : "Se ha adjuntado un nuevo documento al expediente de arbitraje: {$request->nombre_documento}.";

            NotificacionService::notificarInvolucrados($arbitraje, 'arbitraje', $asuntoNoti, $textoNoti);

            DB::commit();

            Log::info('Documento guardado exitosamente', [
                'id'   => $documento->id_proceso_arbitraje_documento,
                'tipo' => $tipoDoc,
                'resub_voucher' => $esResubirVoucher
            ]);

            $mensaje = $esResubirVoucher
                ? 'Voucher enviado correctamente. Tu solicitud volvió al estado Validando.'
                : 'Documento subido correctamente';

            return response()->json([
                'success'   => true,
                'message'   => $mensaje,
                'documento' => $documento
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al subir documento:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar comentario a un documento
     */
    public function comentar(Request $request, $id)
    {
        try {
            $documento = ProcesoArbitrajeDocumento::findOrFail($id);

            $observacionesActuales = $documento->observaciones ?? '';
            $nuevoComentario       = $request->input('observaciones');

            $documento->observaciones = $observacionesActuales
                ? $observacionesActuales . "\n[" . now() . "] " . $nuevoComentario
                : "[" . now() . "] " . $nuevoComentario;

            $documento->save();
            $arbitraje = $documento->proceso->arbitraje;
            if ($arbitraje) {
                NotificacionService::notificarInvolucrados(
                    $arbitraje, 
                    'arbitraje', 
                    'Nueva Observación en Documento', 
                    "Se ha agregado un comentario u observación al documento: {$documento->nombre_original}. Por favor, revise los detalles en su casilla."
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Comentario agregado correctamente'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al comentar documento:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el comentario: ' . $e->getMessage()
            ], 500);
        }
    }
}