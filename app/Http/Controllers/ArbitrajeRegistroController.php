<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Arbitraje;
use App\Models\ProcesoArbitraje;
use App\Models\ProcesoArbitrajePersona;
use App\Models\ProcesoArbitrajeDocumento;

class ArbitrajeRegistroController extends Controller
{
    public function store(Request $request)
    {
        // DEBUG: Ver qué llega
        \Log::info('Request completo:', $request->all());
        
        // Validación adicional para el link de Drive
        $request->validate([
            'drive_link' => 'nullable|url',
            'nombre_documento_link' => 'nullable|string|max:255',
        ]);
        
        DB::beginTransaction();

        try {
            // 1️⃣ ARBITRAJE
            $arbitraje = Arbitraje::create([
                'user_id'            => Auth::id(),
                'fecha_inicio'       => now(),
                'fecha_finalizacion' => null,
                'nombre_materia'     => $request->nombre_materia,
                'descripcion'        => $request->descripcion,
                'estado'             => 'validando'
            ]);

            // 2️⃣ PROCESO INICIAL
            $proceso = ProcesoArbitraje::create([
                'arbitraje_id' => $arbitraje->id_arbitraje,
                'fecha'        => now(),
                'nombre'       => $request->nombre_proceso,
                'descripcion'  => $request->descripcion_proceso,
                'estado'       => 'Iniciado'
            ]);

            // 3️⃣ PERSONAS (corregido)
            $personas = $request->input('personas', []);
            
            foreach ($personas as $persona) {
                ProcesoArbitrajePersona::create([
                    'arbitraje_id' => $arbitraje->id_arbitraje,
                    'dni'          => $persona['dni'],
                    'tipo'         => $persona['tipo']
                ]);
            }

            // 4️⃣ DOCUMENTOS - AHORA DOS DOCUMENTOS
            
            // Primer documento: Voucher (archivo físico)
            if ($request->hasFile('voucher')) {
                $file = $request->file('voucher');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads/vouchers', $filename, 'public');
                
                ProcesoArbitrajeDocumento::create([
                    'proceso_arbitraje_id' => $proceso->id_proceso_arbitraje,
                    'fecha_subida'         => now(),
                    'tipo_documento'       => 'imagen',
                    'nombre_original'      => $file->getClientOriginalName(),
                    'ruta_archivo'         => '/storage/' . $path
                ]);
            }

            // Segundo documento: Link de Drive (si se proporciona)
            if ($request->filled('drive_link')) {
                ProcesoArbitrajeDocumento::create([
                    'proceso_arbitraje_id' => $proceso->id_proceso_arbitraje,
                    'fecha_subida'         => now(),
                    'tipo_documento'       => 'otro',
                    'nombre_original'      => $request->nombre_documento_link ?? 'Documento Drive',
                    'ruta_archivo'         => $request->drive_link
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Arbitraje registrado correctamente',
                'arbitraje_id' => $arbitraje->id_arbitraje,
                'documentos_subidos' => [
                    'voucher' => $request->hasFile('voucher') ? 'Sí' : 'No',
                    'drive_link' => $request->filled('drive_link') ? 'Sí' : 'No'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error en arbitraje:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al registrar arbitraje',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}