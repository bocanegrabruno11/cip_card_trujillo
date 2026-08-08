<?php

namespace App\Http\Controllers;

use App\Models\AsistenteFinalCipcdll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EnviarTarjetaController extends Controller
{
    /**
     * Enviar tarjetas por correo en lotes de 20.
     *
     * Recibe: { lote: int, tamano: int (default 20) }
     * Devuelve: { procesados, total, lote_actual, finalizado, enviados, errores, mensaje }
     */
    public function enviarATodos(Request $request)
    {
        $loteActual = max(1, (int) $request->input('lote', 1));
        $tamano     = max(1, min(50, (int) $request->input('tamano', 20)));
        $offset     = ($loteActual - 1) * $tamano;

        $log   = [];
        $log[] = "▶ enviarATodos() lote={$loteActual} tamano={$tamano} offset={$offset} — " . now()->toDateTimeString();

        // ── 1. Config ──────────────────────────────────────────────────────
        $resendKey = config('resend.api_key') ?? env('RESEND_API_KEY');
        $fromMail  = config('mail.from.address', 'NO DEFINIDO');

        if (!$resendKey) {
            return response()->json([
                'mensaje'   => 'RESEND_API_KEY no configurada.',
                'debug'     => $log,
                'error'     => 'Sin API key',
            ], 500);
        }

        // ── 2. Total de registros ──────────────────────────────────────────
        $total = AsistenteFinalCipcdll::whereNotNull('correo')
            ->where('correo', '!=', '')
            ->count();

        $log[] = "📊 Total con correo: {$total} | Procesando lote {$loteActual}";

        if ($total === 0) {
            return response()->json([
                'mensaje'    => 'Sin asistentes con correo.',
                'procesados' => 0,
                'total'      => 0,
                'lote_actual'=> $loteActual,
                'finalizado' => true,
                'enviados'   => 0,
                'errores'    => [],
                'debug'      => $log,
            ]);
        }

        // ── 3. Cargar sólo el lote actual ─────────────────────────────────
        $asistentes = AsistenteFinalCipcdll::whereNotNull('correo')
            ->where('correo', '!=', '')
            ->orderBy('id')         // orden estable
            ->skip($offset)
            ->take($tamano)
            ->get();

        $log[] = "📦 Registros en este lote: {$asistentes->count()}";

        // ── 4. Verificar carpeta ───────────────────────────────────────────
        $carpeta = storage_path('app/public/tarjetas');

        // ── 5. Procesar lote ──────────────────────────────────────────────
        $enviados = 0;
        $errores  = [];

        foreach ($asistentes as $asistente) {
            $cip       = $asistente->cip ?? 'SIN_CIP';
            $resultado = $this->enviarCorreo($asistente, $log);

            if ($resultado['success']) {
                $enviados++;
                $log[] = "✅ [{$cip}] Enviado OK";
            } else {
                $log[] = "❌ [{$cip}] Error: {$resultado['error']}";
                $errores[] = [
                    'cip'    => $cip,
                    'correo' => $asistente->correo,
                    'error'  => $resultado['error'],
                ];
            }
        }

        $procesados  = $offset + $asistentes->count();
        $finalizado  = $procesados >= $total;

        $log[] = "Procesados acumulados: {$procesados}/{$total} | Finalizado: " . ($finalizado ? 'SÍ' : 'NO');
        Log::info(implode("\n", $log));

        return response()->json([
            'mensaje'     => $finalizado ? 'Proceso finalizado.' : 'Lote procesado.',
            'procesados'  => $procesados,
            'total'       => $total,
            'lote_actual' => $loteActual,
            'finalizado'  => $finalizado,
            'enviados'    => $enviados,
            'errores'     => $errores,
        ]);
    }

    /**
     * Enviar correo a un CIP específico (sin lotes, uso puntual).
     */
    public function enviarPorCip(string $cip)
    {
        $log   = [];
        $log[] = "▶ enviarPorCip() — CIP: {$cip} — " . now()->toDateTimeString();

        $asistente = AsistenteFinalCipcdll::where('cip', $cip)->first();

        if (!$asistente) {
            $log[] = "❌ CIP {$cip} no encontrado en BD.";
            return response()->json(['mensaje' => 'Asistente no encontrado.', 'debug' => $log], 404);
        }

        $log[] = "✅ Encontrado: {$asistente->nombres} {$asistente->apellidos} | {$asistente->correo}";

        $resultado = $this->enviarCorreo($asistente, $log);

        if ($resultado['success']) {
            $log[] = "✅ Correo enviado correctamente.";
            return response()->json(['mensaje' => 'Correo enviado correctamente.', 'debug' => $log]);
        }

        $log[] = "❌ Fallo al enviar: {$resultado['error']}";
        return response()->json([
            'mensaje' => 'Error al enviar correo.',
            'error'   => $resultado['error'],
            'debug'   => $log,
        ], 500);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function enviarCorreo(AsistenteFinalCipcdll $asistente, array &$log): array
    {
        try {
            // 1. Ruta de la tarjeta
            $rutaTarjeta = storage_path("app/public/tarjetas/{$asistente->cip}.png");
            $log[] = "   📎 Tarjeta existe: " . (file_exists($rutaTarjeta) ? '✅ SÍ' : '❌ NO');

            if (!file_exists($rutaTarjeta)) {
                return [
                    'success' => false,
                    'error'   => "Tarjeta no encontrada: {$rutaTarjeta}",
                ];
            }

            // 2. Leer y codificar
            $tarjetaBase64  = base64_encode(file_get_contents($rutaTarjeta));
            $nombreCompleto = trim("{$asistente->nombres} {$asistente->apellidos}");
            $fromAddress    = config('mail.from.address', 'no-reply@tudominio.com');
            $resendKey      = config('resend.api_key') ?? env('RESEND_API_KEY');

            // 3. Enviar via Resend API
            $response = Http::withToken($resendKey)
                ->post('https://api.resend.com/emails', [
                    'from'        => $fromAddress,
                    'to'          => [$asistente->correo],
                    'subject'     => '🎟️ Tu Tarjeta Personal de Entrada — CIP CD La Libertad',
                    'html'        => $this->buildHtml($nombreCompleto, $asistente->cip, $asistente->capitulo),
                    'attachments' => [
                        [
                            'filename'     => "tarjeta_{$asistente->cip}.png",
                            'content'      => $tarjetaBase64,
                            'content_type' => 'image/png',
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                throw new \Exception('Resend API error: ' . $response->body());
            }

            $emailId = $response->json('id') ?? 'sin-id';
            $log[]   = "   📨 Resend ID: {$emailId}";

            return ['success' => true];

        } catch (\Throwable $e) {
            $log[] = "   ❌ EXCEPCIÓN: " . $e->getMessage();
            Log::error("EnviarTarjeta error CIP {$asistente->cip}: " . $e->getMessage());

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }

    private function buildHtml(string $nombre, string $cip, ?string $capitulo): string
    {
        $capitulo = $capitulo ?? 'N/A';

        $html  = '<!DOCTYPE html>';
        $html .= '<html lang="es">';
        $html .= '<head>';
        $html .= '<meta charset="UTF-8"/>';
        $html .= '<meta name="viewport" content="width=device-width,initial-scale=1.0"/>';
        $html .= '<title>Tarjeta de Entrada</title>';
        $html .= '</head>';
        $html .= '<body style="margin:0;padding:0;background:#f0f4f8;font-family:\'Segoe UI\',Arial,sans-serif;">';

        $html .= '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:30px 0;">';
        $html .= '<tr><td align="center">';
        $html .= '<table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,.10);max-width:600px;">';

        // Header rojo
        $html .= '<tr>';
        $html .= '<td style="background:linear-gradient(135deg,#7f1d1d 0%,#dc2626 50%,#ef4444 100%);padding:36px 40px;text-align:center;box-shadow:0 8px 25px rgba(220,38,38,0.35);">';
        $html .= '<div style="font-size:42px;margin-bottom:10px;filter:drop-shadow(0 2px 4px rgba(0,0,0,0.25));">&#127963;</div>';
        $html .= '<h1 style="margin:0;color:#fff;font-size:24px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;text-shadow:0 2px 6px rgba(0,0,0,0.25);">COLEGIO DE INGENIEROS DEL PERÚ</h1>';
        $html .= '<p style="margin:10px 0 0;color:#fee2e2;font-size:13px;letter-spacing:3px;font-weight:600;text-transform:uppercase;">CONSEJO DEPARTAMENTAL LA LIBERTAD</p>';
        $html .= '</td>';
        $html .= '</tr>';

        // Cuerpo
        $html .= '<tr>';
        $html .= '<td style="padding:38px 40px 28px;">';
        $html .= '<p style="margin:0 0 4px;color:#6b7280;font-size:14px;">Estimado(a) colegiado(a),</p>';
        $html .= '<h2 style="margin:0 0 24px;color:#1e40af;font-size:20px;font-weight:700;">' . htmlspecialchars($nombre) . '</h2>';

        // Caja info
        $html .= '<table width="100%" cellpadding="0" cellspacing="0" style="background:#eff6ff;border-left:4px solid #AD2B2E;border-radius:8px;margin-bottom:26px;">';
        $html .= '<tr><td style="padding:18px 20px;">';
        $html .= '<p style="margin:0;font-size:15px;color:#1e3a5f;line-height:1.65;">';
        $html .= '&#10003; &nbsp;<strong>Tu información ha sido validada.</strong><br/>';
        $html .= 'Esta es tu <strong>tarjeta personal de entrada</strong> al evento.<br/>';
        $html .= 'La encontrarás <strong>adjunta</strong> a este correo como archivo PNG.';
        $html .= '</p>';
        $html .= '</td></tr>';
        $html .= '</table>';

        // Tabla datos
        $html .= '<table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:26px;">';
        $html .= '<tr style="background:#f8fafc;">';
        $html .= '<td style="padding:13px 20px;border-bottom:1px solid #e5e7eb;">';
        $html .= '<span style="font-size:12px;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;">Nombre completo</span><br/>';
        $html .= '<strong style="color:#111827;font-size:15px;">' . htmlspecialchars($nombre) . '</strong>';
        $html .= '</td></tr>';
        $html .= '<tr>';
        $html .= '<td style="padding:13px 20px;border-bottom:1px solid #e5e7eb;">';
        $html .= '<span style="font-size:12px;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;">CIP</span><br/>';
        $html .= '<strong style="color:#111827;font-size:15px;">' . htmlspecialchars($cip) . '</strong>';
        $html .= '</td></tr>';
        $html .= '</table>';

        // Aviso amarillo
        $html .= '<table width="100%" cellpadding="0" cellspacing="0" style="background:#fef3c7;border:1px solid #fbbf24;border-radius:8px;margin-bottom:28px;">';
        $html .= '<tr><td style="padding:16px 20px;text-align:center;">';
        $html .= '<p style="margin:0;font-size:14px;color:#92400e;font-weight:700;">Esta tarjeta es PERSONAL e INTRANSFERIBLE</p>';
        $html .= '<p style="margin:6px 0 0;font-size:13px;color:#b45309;">Preséntala al ingresar junto con tu DNI. No se aceptarán transferencias.</p>';
        $html .= '</td></tr>';
        $html .= '</table>';

        // Instrucciones
        $html .= '<p style="margin:0 0 12px;color:#374151;font-size:14px;font-weight:600;">&#128203; ¿Cómo usar tu tarjeta?</p>';
        $html .= '<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">';
        $html .= '<tr><td style="padding:5px 0;font-size:14px;color:#4b5563;">1. &nbsp; Descarga el archivo PNG adjunto a este correo</td></tr>';
        $html .= '<tr><td style="padding:5px 0;font-size:14px;color:#4b5563;">2. &nbsp; Muéstralo desde tu celular o imprímelo</td></tr>';
        $html .= '<tr><td style="padding:5px 0;font-size:14px;color:#4b5563;">3. &nbsp; Preséntalo junto a tu DNI al personal de ingreso</td></tr>';
        $html .= '</table>';
        $html .= '</td></tr>';

        // Footer rojo
        $html .= '<tr>';
        $html .= '<td style="background:#AD2B2E;padding:22px 40px;text-align:center;">';
        $html .= '<p style="margin:0;color:#93c5fd;font-size:12px;line-height:1.6;">';
        $html .= '&copy; 2026 Colegio de Ingenieros del Per&uacute; &mdash; CD La Libertad<br/>';
        $html .= 'Este mensaje fue generado autom&aacute;ticamente, por favor no responder.';
        $html .= '</p>';
        $html .= '</td>';
        $html .= '</tr>';

        $html .= '</table>';
        $html .= '</td></tr>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        return $html;
    }
}