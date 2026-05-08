<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsistenteCipcdll;

class AsistenciaQrController extends Controller
{
    /**
     * 📱 Buscar asistente por DNI (escáner QR)
     */
    public function buscarPorDni(Request $request)
    {
        try {
            $dni = $request->dni;

            if (!$dni) {
                return response()->json([
                    'success' => false,
                    'message' => 'DNI no proporcionado'
                ]);
            }

            $asistente = AsistenteCipcdll::where('dni', $dni)->first();

            if (!$asistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún registro con ese DNI'
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
            \Log::error('Error en buscarPorDni: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Marcar asistencia desde QR
     */
    public function marcarAsistenciaQr(Request $request)
    {
        try {
            $id = $request->id;

            $asistente = AsistenteCipcdll::find($id);

            if (!$asistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asistente no encontrado'
                ]);
            }

            if ($asistente->estado !== 'aprobado') {
                return response()->json([
                    'success' => false,
                    'message' => 'El asistente no está aprobado'
                ]);
            }

            if ($asistente->asistio === 1) {
                return response()->json([
                    'success' => false,
                    'already' => true,
                    'message' => 'La asistencia ya fue registrada anteriormente'
                ]);
            }

            $asistente->asistio = 1;
            $asistente->save();

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
}