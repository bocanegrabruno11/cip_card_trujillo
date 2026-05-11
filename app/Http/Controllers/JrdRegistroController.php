<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Jrd;
use App\Models\ProcesoJrd;
use App\Models\ProcesoJrdPersona;
use App\Models\ProcesoJrdDocumento;
use App\Models\EtapaJrd;
use Illuminate\Support\Facades\Log;

class JrdRegistroController extends Controller
{
    public function store(Request $request)
    {
        try {
            Log::info('=== INICIO REGISTRO JRD ===');
            Log::info('Datos recibidos:', $request->except(['voucher', 'escrito']));

            $request->validate([
                'drive_link'             => 'nullable|url',
                'nombre_documento_link'  => 'nullable|string|max:255',
                'cuantia'                => 'nullable|string|max:255',
                'tasa_solicitud'         => 'nullable|string|max:255',
                'nombre_materia'         => 'required|string|max:255',
                'pretenciones'           => 'required|string',
                'voucher'                => 'required|file|mimes:jpg,jpeg,png,pdf|max:20480',
                'escrito'                => 'nullable|file|mimes:pdf|max:20480',
            ]);

            DB::beginTransaction();

            // 1️⃣ Buscar la primera etapa activa
            $primeraEtapa = EtapaJrd::where('estado', 1)
                ->orderBy('id', 'asc')
                ->first();

            if (!$primeraEtapa) {
                throw new \Exception('No hay etapas activas configuradas. Contacte al administrador.');
            }

            // 2️⃣ Generar número de expediente
            $year = now()->year;

            $count = Jrd::whereYear('fecha_inicio', $year)
                ->lockForUpdate()
                ->count() + 1;

            $correlativo      = str_pad($count, 3, '0', STR_PAD_LEFT);
            $numeroExpediente = "EXP {$correlativo}-{$year}-JPRD-CARD-CIP-CDLL";

            // 3️⃣ Crear JRD
            $jrd = Jrd::create([
                'user_id'                   => Auth::id(),
                'fecha_inicio'              => now(),
                'fecha_finalizacion'        => null,
                'nombre_materia'            => $request->nombre_materia,
                'pretenciones'              => $request->pretenciones,
                'controversia'              => $request->controversia,
                'fundamentos_hecho'         => $request->fundamentos_hecho,
                'cuantia'                   => $request->cuantia,
                'designacion_adjudicadores' => $request->designacion_adjudicadores,
                'tasa_solicitud'            => $request->tasa_solicitud,
                'numero_expediente'         => $numeroExpediente,
                'estado'                    => 'en proceso',
            ]);

            Log::info('JRD creado:', [
                'id'                => $jrd->id_jrd,
                'numero_expediente' => $numeroExpediente,
            ]);

            // 4️⃣ Crear PROCESO INICIAL
            $proceso = ProcesoJrd::create([
                'jrd_id'             => $jrd->id_jrd,
                'fecha_creacion'     => now(),
                'fecha_finalizacion' => null,
                'id_etapa_jrd'       => $primeraEtapa->id,
                'estado'             => 'activo',
            ]);

            Log::info('Proceso creado:', ['id' => $proceso->id_proceso_jrd]);

            // 5️⃣ GUARDAR PERSONAS
            $personas = $request->input('personas', []);

            Log::info('Personas a guardar:', ['count' => count($personas)]);

            foreach ($personas as $index => $persona) {
                if (!isset($persona['dni']) || !isset($persona['tipo'])) {
                    Log::warning("Persona {$index} sin DNI o tipo, omitiendo:", $persona);
                    continue;
                }

                $datosPersona = [
                    'jrd_id'    => $jrd->id_jrd,
                    'dni'       => $persona['dni'],
                    'tipo'      => $persona['tipo'],
                    'nombres_completos' => $persona['nombres_completos'] ?? null,
                    'razon_social'      => $persona['razon_social'] ?? null,
                    'correo'    => $persona['correo']    ?? null,
                    'telefono'  => $persona['telefono']  ?? null,
                    'ruc'       => $persona['ruc']       ?? null,
                    'direccion' => $persona['direccion'] ?? null,
                ];

                Log::info("Guardando persona {$index}:", $datosPersona);
                $personaGuardada = ProcesoJrdPersona::create($datosPersona);
                Log::info("Persona {$index} guardada con ID: " . $personaGuardada->id_proceso_jrd_persona);
            }

            // 6️⃣ SUBIR VOUCHER
            $voucher = $request->file('voucher');
            $vNombre = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $voucher->getClientOriginalName());
            $vPath   = $voucher->storeAs('uploads/vouchers', $vNombre, 'public');

            $docVoucher = ProcesoJrdDocumento::create([
                'proceso_jrd_id'  => $proceso->id_proceso_jrd,
                'fecha_subida'    => now(),
                'tipo_documento'  => 'voucher',
                'nombre_original' => 'Voucher de Pago - ' . now()->format('d/m/Y'),
                'ruta_archivo'    => '/storage/' . $vPath,
                'observaciones'   => 'Voucher de pago para tasa de solicitud',
                'user_id'         => Auth::id(),
            ]);

            Log::info('Voucher guardado:', ['id' => $docVoucher->id_proceso_jrd_documento]);

            // 7️⃣ ESCRITO PDF (opcional)
            if ($request->hasFile('escrito')) {
                $escrito = $request->file('escrito');
                $eNombre = time() . '_escrito_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $escrito->getClientOriginalName());
                $ePath   = $escrito->storeAs('uploads/escritos', $eNombre, 'public');

                if ($ePath) {
                    $docEscrito = ProcesoJrdDocumento::create([
                        'proceso_jrd_id'  => $proceso->id_proceso_jrd,
                        'fecha_subida'    => now(),
                        'tipo_documento'  => 'escrito',
                        'nombre_original' => $escrito->getClientOriginalName(),
                        'ruta_archivo'    => '/storage/' . $ePath,
                        'observaciones'   => 'Escrito de solicitud de JPRD',
                        'user_id'         => Auth::id(),
                    ]);

                    Log::info('Escrito guardado:', ['id' => $docEscrito->id_proceso_jrd_documento]);
                }
            }

            // 8️⃣ DOCUMENTO DE DRIVE (opcional)
            if ($request->filled('drive_link')) {
                $docDrive = ProcesoJrdDocumento::create([
                    'proceso_jrd_id'  => $proceso->id_proceso_jrd,
                    'fecha_subida'    => now(),
                    'tipo_documento'  => 'otro',
                    'nombre_original' => $request->nombre_documento_link ?? 'Documento Drive',
                    'ruta_archivo'    => $request->drive_link,
                    'observaciones'   => 'Documento adicional de Google Drive',
                    'user_id'         => Auth::id(),
                ]);

                Log::info('Documento Drive guardado:', ['id' => $docDrive->id_proceso_jrd_documento]);
            }

            DB::commit();

            Log::info('=== JRD REGISTRADO EXITOSAMENTE ===', [
                'jrd_id'               => $jrd->id_jrd,
                'numero_expediente'    => $numeroExpediente,
                'personas_registradas' => count($personas),
            ]);

            return response()->json([
                'success'           => true,
                'message'           => 'JRD registrado correctamente',
                'jrd_id'            => $jrd->id_jrd,
                'numero_expediente' => $numeroExpediente,
                'etapa_actual'      => $primeraEtapa->nombre,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('=== ERROR DE VALIDACIÓN ===', [
                'errors'  => $e->errors(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Error de validación',
                'detalle' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== ERROR EN REGISTRO JRD ===', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Error al registrar JRD',
                'detalle' => $e->getMessage(),
            ], 500);
        }
    }
}