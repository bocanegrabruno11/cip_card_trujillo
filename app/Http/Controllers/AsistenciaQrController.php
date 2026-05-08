<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsistenteCipcdll;

class AsistenciaQrController extends Controller
{
    /**
     * Lee el campo 'dni' tanto de JSON body como de form-data / query string.
     */
    private function obtenerDniDeRequest(Request $request): string
    {
        // 1. Intentar leer del body JSON directamente (cuando Content-Type: application/json)
        $raw = $request->getContent();
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['dni'])) {
                return trim((string) $decoded['dni']);
            }
        }

        // 2. Fallback: input normal (form-data, query string)
        return trim((string) $request->input('dni', ''));
    }

    public function buscarPorDni(Request $request)
    {
        try {
            $rawData = $this->obtenerDniDeRequest($request);

            \Log::info('QR/DNI recibido (raw): ' . $rawData);

            if ($rawData === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'Dato vacío',
                ]);
            }

            $dni = null;

            // 1. Intentar parsear como JSON anidado (ej: QR que contiene {"dni":"12345678"})
            $decoded = json_decode($rawData, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['dni'])) {
                $dni = trim((string) $decoded['dni']);
                \Log::info('DNI extraído de JSON anidado: ' . $dni);
            }

            // 2. Intentar extraer de URL (ej: https://example.com/asistente/12345678)
            if (!$dni && filter_var($rawData, FILTER_VALIDATE_URL)) {
                $segmentos = explode('/', rtrim($rawData, '/'));
                $ultimo    = end($segmentos);
                if (preg_match('/^\d{6,12}$/', $ultimo)) {
                    $dni = $ultimo;
                    \Log::info('DNI extraído de URL: ' . $dni);
                }
            }

            // 3. Solo dígitos si tiene longitud válida (6-12 chars)
            if (!$dni) {
                $soloDigitos = preg_replace('/\D/', '', $rawData);
                if (strlen($soloDigitos) >= 6 && strlen($soloDigitos) <= 12) {
                    $dni = $soloDigitos;
                    \Log::info('DNI extraído como dígitos: ' . $dni);
                } else {
                    // Usar tal cual si no hay dígitos suficientes
                    $dni = $rawData;
                    \Log::info('DNI usado tal cual: ' . $dni);
                }
            }

            \Log::info('DNI final a buscar: ' . $dni);

            // ── Búsqueda robusta ──────────────────────────────────────────────
            // 1. Coincidencia exacta (con trim en BD usando whereRaw)
            $asistente = AsistenteCipcdll::whereRaw('TRIM(dni) = ?', [$dni])->first();

            // 2. Si no encontró, buscar con LIKE (por si hay espacios internos u otros formatos)
            if (!$asistente) {
                $asistente = AsistenteCipcdll::where('dni', 'LIKE', '%' . $dni . '%')->first();
                if ($asistente) {
                    \Log::info('Encontrado con LIKE: ' . $asistente->dni);
                }
            }

            // 3. Si el rawData era diferente al DNI limpio, intentar también con el raw
            if (!$asistente && $rawData !== $dni) {
                $asistente = AsistenteCipcdll::whereRaw('TRIM(dni) = ?', [trim($rawData)])->first();
                if ($asistente) {
                    \Log::info('Encontrado con rawData: ' . $rawData);
                }
            }

            if (!$asistente) {
                \Log::warning('No encontrado con DNI: ' . $dni . ' | raw: ' . $rawData);
                return response()->json([
                    'success'     => false,
                    'message'     => 'No encontrado',
                    'dni_buscado' => $dni,
                    'raw_qr'      => $rawData,
                ]);
            }

            \Log::info('Encontrado: ' . $asistente->nombres . ' | Estado: ' . $asistente->estado);

            return response()->json([
                'success'   => true,
                'id'        => $asistente->id,
                'cip'       => $asistente->cip,
                'dni'       => $asistente->dni,
                'nombres'   => $asistente->nombres,
                'apellidos' => $asistente->apellidos,
                'capitulo'  => $asistente->capitulo,
                'celular'   => $asistente->celular,
                'correo'    => $asistente->correo,
                'estado'    => $asistente->estado,
                'asistio'   => $asistente->asistio,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en buscarPorDni: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function marcarAsistenciaQr(Request $request)
    {
        try {
            // Leer id desde JSON body o form-data
            $raw = $request->getContent();
            $id  = null;

            if ($raw) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['id'])) {
                    $id = $decoded['id'];
                }
            }

            if (!$id) {
                $id = $request->input('id');
            }

            if (!$id) {
                return response()->json(['success' => false, 'message' => 'ID no proporcionado']);
            }

            $asistente = AsistenteCipcdll::find($id);

            if (!$asistente) {
                return response()->json(['success' => false, 'message' => 'Asistente no encontrado']);
            }

            if ($asistente->estado !== 'aprobado') {
                return response()->json(['success' => false, 'message' => 'El asistente no está aprobado']);
            }

            if ($asistente->asistio == 1) {
                return response()->json([
                    'success' => false,
                    'already' => true,
                    'message' => 'La asistencia ya fue registrada anteriormente',
                ]);
            }

            $asistente->asistio = 1;
            $asistente->save();

            \Log::info('Asistencia marcada: ' . $asistente->nombres . ' (ID: ' . $id . ')');

            return response()->json(['success' => true, 'message' => 'Asistencia marcada correctamente']);

        } catch (\Exception $e) {
            \Log::error('Error en marcarAsistenciaQr: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage(),
            ], 500);
        }
    }
}