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
            Log::info('Datos recibidos:', $request->all());

            $request->validate([
                'drive_link'             => 'nullable|url',
                'nombre_documento_link'  => 'nullable|string|max:255',
                'nombre_documento'       => 'required|string|max:255',
                'cuantia'                => 'nullable|string|max:255',
                'tasa_solicitud'         => 'nullable|string|max:255',
                'nombre_materia'         => 'required|string|max:255',
                'pretenciones'           => 'required|string',
            ]);

            DB::beginTransaction();

            // 1️⃣ Buscar la primera etapa activa
            $primeraEtapa = EtapaJrd::where('estado', 1)
                ->orderBy('id', 'asc')
                ->first();

            if (!$primeraEtapa) {
                throw new \Exception('No hay etapas activas configuradas. Contacte al administrador.');
            }

            // 2️⃣ Crear JRD
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
                'estado'                    => 'en proceso'
            ]);

            Log::info('JRD creado:', ['id' => $jrd->id_jrd]);

            // 3️⃣ Crear PROCESO INICIAL
            $proceso = ProcesoJrd::create([
                'jrd_id'             => $jrd->id_jrd,
                'fecha_creacion'     => now(),
                'fecha_finalizacion' => null,
                'id_etapa_jrd'       => $primeraEtapa->id,
                'estado'             => 'activo'
            ]);

            Log::info('Proceso creado:', ['id' => $proceso->id_proceso_jrd]);

            // 4️⃣ GUARDAR PERSONAS
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
                    'nombres'   => $persona['nombres']   ?? null,
                    'apellidos' => $persona['apellidos'] ?? null,
                    'correo'    => $persona['correo']    ?? null,
                    'telefono'  => $persona['telefono']  ?? null,
                    'ruc'       => $persona['ruc']       ?? null,
                ];

                Log::info("Guardando persona {$index}:", $datosPersona);

                $personaGuardada = ProcesoJrdPersona::create($datosPersona);
                Log::info("Persona {$index} guardada con ID: " . $personaGuardada->id_proceso_jrd_persona);
            }

            // 5️⃣ SUBIR VOUCHER
            if ($request->hasFile('voucher')) {
                $file     = $request->file('voucher');
                $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
                $path     = $file->storeAs('uploads/vouchers', $filename, 'public');

                $documento = ProcesoJrdDocumento::create([
                    'proceso_jrd_id'  => $proceso->id_proceso_jrd,
                    'fecha_subida'    => now(),
                    'tipo_documento'  => 'voucher',
                    'nombre_original' => $request->nombre_documento ?? $file->getClientOriginalName(),
                    'ruta_archivo'    => '/storage/' . $path,
                    'observaciones'   => null,
                    'user_id'         => Auth::id()
                ]);

                Log::info('Voucher guardado:', ['id' => $documento->id_proceso_jrd_documento]);
            } else {
                Log::warning('No se recibió archivo voucher');
                throw new \Exception('El voucher es obligatorio');
            }

            // 6️⃣ DOCUMENTO DE DRIVE (opcional)
            if ($request->filled('drive_link')) {
                $documentoDrive = ProcesoJrdDocumento::create([
                    'proceso_jrd_id'  => $proceso->id_proceso_jrd,
                    'fecha_subida'    => now(),
                    'tipo_documento'  => 'otro',
                    'nombre_original' => $request->nombre_documento_link ?? 'Documento Drive',
                    'ruta_archivo'    => $request->drive_link,
                    'observaciones'   => null,
                    'user_id'         => Auth::id()
                ]);

                Log::info('Documento Drive guardado:', ['id' => $documentoDrive->id_proceso_jrd_documento]);
            }

            DB::commit();

            Log::info('=== JRD REGISTRADO EXITOSAMENTE ===', [
                'jrd_id'               => $jrd->id_jrd,
                'personas_registradas' => count($personas)
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'JRD registrado correctamente',
                'jrd_id'       => $jrd->id_jrd,
                'etapa_actual' => $primeraEtapa->nombre
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('=== ERROR DE VALIDACIÓN ===', [
                'errors'  => $e->errors(),
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Error de validación',
                'detalle' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== ERROR EN REGISTRO JRD ===', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Error al registrar JRD',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}