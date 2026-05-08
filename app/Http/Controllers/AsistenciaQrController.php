<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsistenteCipcdll;

class AsistenciaQrController extends Controller
{
    public function buscarPorDni(Request $request)
    {
        try {
            $rawData = trim($request->input('dni', ''));

            \Log::info('QR recibido (raw): ' . $rawData);

            if (!$rawData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dato QR vacío'
                ]);
            }

            $dni = null;

            // 1. Intentar parsear como JSON
            $decoded = json_decode($rawData, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($decoded['dni'])) {
                $dni = trim($decoded['dni']);
                \Log::info('DNI extraído de JSON: ' . $dni);
            }

            // 2. Intentar extraer de URL
            if (!$dni && filter_var($rawData, FILTER_VALIDATE_URL)) {
                $segmentos = explode('/', rtrim($rawData, '/'));
                $ultimo    = end($segmentos);
                if (preg_match('/^\d{6,12}$/', $ultimo)) {
                    $dni = $ultimo;
                    \Log::info('DNI extraído de URL: ' . $dni);
                }
            }

            // 3. Solo dígitos si tiene longitud válida
            if (!$dni) {
                $soloDigitos = preg_replace('/\D/', '', $rawData);
                if (strlen($soloDigitos) >= 6 && strlen($soloDigitos) <= 12) {
                    $dni = $soloDigitos;
                    \Log::info('DNI extraído como dígitos: ' . $dni);
                } else {
                    $dni = $rawData;
                    \Log::info('DNI usado tal cual: ' . $dni);
                }
            }

            \Log::info('DNI final a buscar: ' . $dni);

            $asistente = AsistenteCipcdll::where('dni', $dni)->first();

            if (!$asistente) {
                \Log::warning('No encontrado con DNI: ' . $dni);
                return response()->json([
                    'success'     => false,
                    'message'     => 'No encontrado',
                    'dni_buscado' => $dni,
                    'raw_qr'      => $rawData,
                ]);
            }

            \Log::info('Encontrado: ' . $asistente->nombres . ' - Estado: ' . $asistente->estado);

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
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    public function marcarAsistenciaQr(Request $request)
    {
        try {
            $id = $request->input('id');

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
                    'message' => 'La asistencia ya fue registrada anteriormente'
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
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }
}