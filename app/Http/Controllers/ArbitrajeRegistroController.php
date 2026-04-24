<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Arbitraje;
use App\Models\ProcesoArbitrajePersona;
use App\Models\ProcesoDeArbitraje;
use App\Models\ProcesoArbitrajeDocumento;
use App\Models\EtapaArbitral;

class ArbitrajeRegistroController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Iniciando registro de arbitraje', [
            'user_id' => Auth::id(),
            'request_data' => $request->except(['voucher', 'escrito'])
        ]);

        try {
            // ── Validación ────────────────────────────────────────────────────────
            $validated = $request->validate([
                'nombre_materia'       => 'required|string|max:255',
                'pretenciones'         => 'required|string',
                'cuantia'              => 'nullable|string|max:550',
                'tasa_solicitud'       => 'nullable|string|max:550',
                'designacion_arbitral' => 'nullable|string|max:255',
                'controversia'         => 'nullable|string|max:550',
                // Personas
                'personas'               => 'required|array|min:2',
                'personas.*.dni'         => 'required|string|size:8',
                'personas.*.nombres'     => 'required|string|max:255',
                'personas.*.apellidos'   => 'required|string|max:255',
                'personas.*.tipo'        => 'required|in:Demandante,Demandado',
                'personas.*.correo'      => 'nullable|email|max:255',
                'personas.*.telefono'    => 'nullable|string|max:20',
                'personas.*.ruc'         => 'nullable|string|max:11',
                'personas.*.direccion'   => 'nullable|string|max:550',
                // Documentos
                'voucher'               => 'required|file|mimes:jpg,jpeg,png,pdf|max:20480',
                'escrito'               => 'nullable|file|mimes:pdf|max:20480',
                'drive_link'            => 'nullable|url',
                'nombre_documento_link' => 'nullable|string|max:255',
            ]);

            // Validar que si viene drive_link también venga el nombre y viceversa
            $driveLink   = $request->input('drive_link', '');
            $driveNombre = $request->input('nombre_documento_link', '');

            if (($driveLink && !$driveNombre) || (!$driveLink && $driveNombre)) {
                return response()->json([
                    'error'   => true,
                    'detalle' => 'Si ingresas un enlace de Drive, debes ponerle un nombre, y viceversa.'
                ], 422);
            }

            DB::beginTransaction();

            // ── 1. Crear Arbitraje ────────────────────────────────────────────
            $arbitraje = Arbitraje::create([
                'user_id'              => Auth::id(),
                'fecha_inicio'         => now(),
                'nombre_materia'       => $request->nombre_materia,
                'pretenciones'         => $request->pretenciones,
                'cuantia'              => $request->cuantia              ?? null,
                'tasa_solicitud'       => $request->tasa_solicitud       ?? null,
                'designacion_arbitral' => $request->designacion_arbitral ?? null,
                'controversia'         => $request->controversia         ?? null,
                'fundamentos_hecho'    => $request->fundamentos_hecho    ?? null,
                'estado'               => 'validando',
            ]);

            Log::info('Arbitraje creado', ['id' => $arbitraje->id_arbitraje]);

            // ── 2. Registrar Personas ─────────────────────────────────────────
            foreach ($request->personas as $persona) {
                ProcesoArbitrajePersona::create([
                    'arbitraje_id' => $arbitraje->id_arbitraje,
                    'dni'          => $persona['dni'],
                    'nombres'      => $persona['nombres'],
                    'apellidos'    => $persona['apellidos'],
                    'correo'       => $persona['correo']    ?? null,
                    'telefono'     => $persona['telefono']  ?? null,
                    'ruc'          => $persona['ruc']       ?? null,
                    'tipo'         => $persona['tipo'],
                    'direccion'    => $persona['direccion'] ?? null,
                ]);
            }

            // ── 3. Proceso de Arbitraje (lógica de etapas) ───────────────────
            $etapa = EtapaArbitral::where('estado', 1)
                ->orderBy('id', 'asc')
                ->first();

            if (!$etapa) {
                DB::rollBack();
                Log::error('No hay etapas activas');
                return response()->json([
                    'error'   => true,
                    'detalle' => 'No hay etapas activas configuradas. Contacte al administrador.'
                ], 422);
            }

            $proceso = ProcesoDeArbitraje::create([
                'fecha_creacion'    => now(),
                'id_etapa_arbitral' => $etapa->id,
                'id_arbitraje'      => $arbitraje->id_arbitraje,
                'estado'            => 'iniciado',
            ]);

            Log::info('Proceso creado', ['id' => $proceso->id_proceso_de_arbitraje]);

            // ── 4. Documentos ─────────────────────────────────────────────────

            // 4a. VOUCHER — nombre generado automáticamente
            $voucher   = $request->file('voucher');
            $vNombre   = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $voucher->getClientOriginalName());
            $vRuta     = $voucher->storeAs('uploads/vouchers', $vNombre, 'public');

            if (!$vRuta) {
                DB::rollBack();
                return response()->json([
                    'error'   => true,
                    'detalle' => 'Error al guardar el voucher. Verifica los permisos de la carpeta storage.'
                ], 500);
            }

            ProcesoArbitrajeDocumento::create([
                'id_proceso_de_arbitraje' => $proceso->id_proceso_de_arbitraje,
                'fecha_subida'            => now(),
                'tipo_documento'          => 'voucher',
                'nombre_original'         => 'Voucher de Pago - ' . now()->format('d/m/Y'),
                'ruta_archivo'            => '/storage/' . $vRuta,
                'user_id'                 => Auth::id(),
                'observaciones'           => 'Voucher de pago para tasa de solicitud',
            ]);

            // 4b. ESCRITO PDF (opcional)
            if ($request->hasFile('escrito')) {
                $escrito  = $request->file('escrito');
                $eNombre  = time() . '_escrito_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $escrito->getClientOriginalName());
                $eRuta    = $escrito->storeAs('uploads/escritos', $eNombre, 'public');

                if ($eRuta) {
                    ProcesoArbitrajeDocumento::create([
                        'id_proceso_de_arbitraje' => $proceso->id_proceso_de_arbitraje,
                        'fecha_subida'            => now(),
                        'tipo_documento'          => 'escrito',
                        'nombre_original'         => $escrito->getClientOriginalName(),
                        'ruta_archivo'            => '/storage/' . $eRuta,
                        'user_id'                 => Auth::id(),
                        'observaciones'           => 'Escrito de solicitud de arbitraje',
                    ]);
                }
            }

            // 4c. Link de Google Drive (opcional)
            if ($driveLink) {
                ProcesoArbitrajeDocumento::create([
                    'id_proceso_de_arbitraje' => $proceso->id_proceso_de_arbitraje,
                    'fecha_subida'            => now(),
                    'tipo_documento'          => 'otro',
                    'nombre_original'         => $driveNombre,
                    'ruta_archivo'            => $driveLink,
                    'user_id'                 => Auth::id(),
                    'observaciones'           => 'Documento adicional de Google Drive',
                ]);
            }

            DB::commit();

            Log::info('Arbitraje registrado exitosamente', ['id' => $arbitraje->id_arbitraje]);

            return response()->json([
                'success'   => true,
                'mensaje'   => 'Arbitraje registrado correctamente.',
                'arbitraje' => $arbitraje->id_arbitraje,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación', ['errors' => $e->errors()]);
            return response()->json([
                'error'   => true,
                'detalle' => 'Error de validación',
                'errores' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar arbitraje', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error'   => true,
                'detalle' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }
}