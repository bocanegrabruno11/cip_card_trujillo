<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Arbitraje;
use App\Models\ProcesoDeArbitraje;
use App\Models\ProcesoArbitrajeDocumento;
use App\Models\EtapaArbitral;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VoucherController extends Controller
{
    public function procesar(Request $request, $id_arbitraje, $id_documento)
    {
        try {
            DB::beginTransaction();
            
            $arbitraje = Arbitraje::findOrFail($id_arbitraje);
            $documento = ProcesoArbitrajeDocumento::findOrFail($id_documento);
            
            // Verificar que el documento sea un voucher
            if ($documento->tipo_documento !== 'voucher') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este documento no es un voucher'
                ], 400);
            }
            
            // Verificar que el arbitraje esté en estado 'validando'
            if ($arbitraje->estado !== 'validando') {
                return response()->json([
                    'success' => false,
                    'message' => 'El arbitraje no está en estado de validación'
                ], 400);
            }
            
            $accion = $request->input('accion');
            
            if ($accion === 'aceptar') {
                // ACEPTAR VOUCHER
                
                // 1. Actualizar el comentario del documento
                $documento->observaciones = ($documento->observaciones ? $documento->observaciones . "\n" : '') 
                    . "[ACEPTADO] Voucher validado por administrador el " . now();
                $documento->save();
                
                // 2. Cambiar estado del arbitraje a 'iniciado'
                $arbitraje->estado = 'iniciado';
                $arbitraje->save();
                
                // 3. Crear el primer proceso (si no existe)
                $procesoExistente = ProcesoDeArbitraje::where('id_arbitraje', $id_arbitraje)->first();
                
                if (!$procesoExistente) {
                    $primeraEtapa = EtapaArbitral::where('estado', 1)
                        ->orderBy('id', 'asc')
                        ->first();
                    
                    if ($primeraEtapa) {
                        ProcesoDeArbitraje::create([
                            'fecha_creacion' => now(),
                            'id_etapa_arbitral' => $primeraEtapa->id,
                            'id_arbitraje' => $id_arbitraje,
                            'estado' => 'iniciado'
                        ]);
                    }
                }
                NotificacionService::notificarTitular(
                    $arbitraje, 
                    'arbitraje', 
                    'Voucher de Pago Aprobado', 
                    'Su comprobante de pago ha sido validado correctamente por la administración. El proceso de arbitraje ha sido iniciado formalmente.'
                );
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Voucher aceptado. El arbitraje ha sido iniciado correctamente.'
                ]);
                
            } elseif ($accion === 'rechazar') {
                // RECHAZAR VOUCHER
                
                $motivo = $request->input('motivo', 'No se especificó motivo');
                
                // 1. Actualizar el documento con el motivo de rechazo
                $documento->observaciones = ($documento->observaciones ? $documento->observaciones . "\n" : '') 
                    . "[RECHAZADO] Motivo: {$motivo} - Fecha: " . now();
                $documento->save();
                
                // 2. Cambiar estado del arbitraje a 'observado'
                $arbitraje->estado = 'observado';
                $arbitraje->save();
                NotificacionService::notificarTitular(
                    $arbitraje, 
                    'arbitraje', 
                    'Pago Observado / Rechazado', 
                    "Su comprobante de pago ha sido observado por la administración. Motivo: {$motivo}. Por favor, verifique su registro y vuelva a subir el voucher correcto."
                );
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Voucher rechazado. El arbitraje ha sido marcado como observado.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Acción no válida'
                ], 400);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar voucher:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }
}