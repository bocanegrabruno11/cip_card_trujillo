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

            \Log::info('=== BÚSQUEDA QR ===');
            \Log::info('QR recibido (raw): ' . $rawData);
            \Log::info('Tipo de dato recibido: ' . gettype($rawData));
            \Log::info('Longitud: ' . strlen($rawData));

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

            // 3. Extraer solo números (prioridad principal)
            if (!$dni) {
                $soloDigitos = preg_replace('/[^0-9]/', '', $rawData);
                if (strlen($soloDigitos) >= 6 && strlen($soloDigitos) <= 12) {
                    $dni = $soloDigitos;
                    \Log::info('DNI extraído como dígitos: ' . $dni);
                } else {
                    $dni = $rawData;
                    \Log::info('DNI usado tal cual: ' . $dni);
                }
            }

            \Log::info('DNI final a buscar: ' . $dni);

            // Búsqueda mejorada - múltiples intentos
            $asistente = null;
            
            // Intento 1: Búsqueda exacta como string
            $asistente = AsistenteCipcdll::where('dni', '=', $dni)->first();
            
            // Intento 2: Si no funciona, buscar como integer
            if (!$asistente && is_numeric($dni)) {
                \Log::info('Intentando búsqueda como integer');
                $asistente = AsistenteCipcdll::where('dni', '=', (int)$dni)->first();
            }
            
            // Intento 3: Búsqueda con TRIM por si hay espacios
            if (!$asistente) {
                \Log::info('Intentando búsqueda con TRIM');
                $asistente = AsistenteCipcdll::whereRaw('TRIM(dni) = ?', [$dni])->first();
            }
            
            // Intento 4: Búsqueda LIKE (último recurso)
            if (!$asistente) {
                \Log::info('Intentando búsqueda LIKE');
                $asistente = AsistenteCipcdll::where('dni', 'LIKE', "%{$dni}%")->first();
            }

            if (!$asistente) {
                \Log::warning('No encontrado con DNI: ' . $dni);
                
                // Debug: Mostrar algunos DNIs de la BD para comparar
                $primerosDnis = AsistenteCipcdll::limit(5)->pluck('dni');
                \Log::info('Primeros 5 DNIs en BD: ' . json_encode($primerosDnis));
                
                return response()->json([
                    'success'     => false,
                    'message'     => 'No encontrado',
                    'dni_buscado' => $dni,
                    'raw_qr'      => $rawData,
                    'debug_dnis_bd' => $primerosDnis
                ]);
            }

            \Log::info('✅ Encontrado: ' . $asistente->nombres . ' - Estado: ' . $asistente->estado);

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

            \Log::info('=== MARCAR ASISTENCIA ===');
            \Log::info('ID recibido: ' . $id);

            if (!$id) {
                return response()->json([
                    'success' => false, 
                    'message' => 'ID no proporcionado'
                ]);
            }

            $asistente = AsistenteCipcdll::find($id);

            if (!$asistente) {
                \Log::warning('Asistente no encontrado con ID: ' . $id);
                return response()->json([
                    'success' => false, 
                    'message' => 'Asistente no encontrado'
                ]);
            }

            \Log::info('Asistente encontrado: ' . $asistente->nombres);
            \Log::info('Estado actual: ' . $asistente->estado);
            \Log::info('Asistio actual: ' . $asistente->asistio);

            if ($asistente->estado !== 'aprobado') {
                \Log::warning('Intento de marcar asistencia para no aprobado: ' . $asistente->estado);
                return response()->json([
                    'success' => false, 
                    'message' => 'El asistente no está aprobado'
                ]);
            }

            if ($asistente->asistio == 1) {
                \Log::warning('Intento duplicado de asistencia para: ' . $asistente->nombres);
                return response()->json([
                    'success' => false,
                    'already' => true,
                    'message' => 'La asistencia ya fue registrada anteriormente'
                ]);
            }

            // Marcar asistencia
            $asistente->asistio = 1;
            $asistente->save();

            \Log::info('✅ Asistencia marcada exitosamente: ' . $asistente->nombres . ' (ID: ' . $id . ')');

            return response()->json([
                'success' => true, 
                'message' => 'Asistencia marcada correctamente'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en marcarAsistenciaQr: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    // Método adicional para debug por GET
    public function buscarPorDniGet($dni)
    {
        try {
            \Log::info('=== BÚSQUEDA GET DEBUG ===');
            \Log::info('DNI recibido por GET: ' . $dni);
            
            $asistente = AsistenteCipcdll::where('dni', $dni)->first();
            
            if (!$asistente) {
                // Intentar como integer
                $asistente = AsistenteCipcdll::where('dni', (int)$dni)->first();
            }
            
            if (!$asistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No encontrado',
                    'dni_buscado' => $dni
                ]);
            }
            
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
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}