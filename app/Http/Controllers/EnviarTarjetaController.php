<?php

namespace App\Http\Controllers;

use App\Models\AsistenteFinalCipcdll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Resend\Laravel\Facades\Resend;

class EnviarTarjetaController extends Controller
{
    public function enviarATodos()
    {
        $log = [];
        $log[] = '════════════════════════════════════════';
        $log[] = '▶ INICIO enviarATodos() — ' . now()->toDateTimeString();
        $log[] = '════════════════════════════════════════';

        // ── 1. Verificar config de Resend ─────────────────────────────────
        $resendKey  = config('resend.api_key') ?? env('RESEND_API_KEY');
        $fromMail   = config('mail.from.address', 'NO DEFINIDO');
        $log[] = "📧 FROM  : {$fromMail}";
        $log[] = "🔑 RESEND KEY: " . ($resendKey ? substr($resendKey, 0, 8) . '...' : '❌ NO ENCONTRADA');

        // ── 2. Consulta a BD ──────────────────────────────────────────────
        $log[] = '';
        $log[] = '── Consultando BD ──────────────────────';
        try {
            $asistentes = AsistenteFinalCipcdll::whereNotNull('correo')
                ->where('correo', '!=', '')
                ->get();
            $log[] = "✅ Registros encontrados: {$asistentes->count()}";
        } catch (\Throwable $e) {
            $msg = "❌ ERROR al consultar BD: " . $e->getMessage();
            $log[] = $msg;
            Log::error($msg);
            return response()->json([
                'mensaje' => 'Error al consultar la base de datos.',
                'debug'   => $log,
                'error'   => $e->getMessage(),
            ], 500);
        }

        if ($asistentes->isEmpty()) {
            $log[] = '⚠ No hay asistentes con correo registrado.';
            return response()->json(['mensaje' => 'Sin asistentes.', 'debug' => $log]);
        }

        // ── 3. Verificar carpeta de tarjetas ──────────────────────────────
        $carpeta = storage_path('app/public/tarjetas');
        $log[] = '';
        $log[] = '── Carpeta de tarjetas ─────────────────';
        $log[] = "📁 Ruta  : {$carpeta}";
        $log[] = "📁 Existe: " . (is_dir($carpeta) ? '✅ SÍ' : '❌ NO');

        if (is_dir($carpeta)) {
            $archivos = glob($carpeta . '/*.png');
            $log[] = "📁 PNGs encontrados: " . count($archivos);
        }

        // ── 4. Enviar correos ─────────────────────────────────────────────
        $log[] = '';
        $log[] = '── Procesando asistentes ───────────────';

        $enviados = 0;
        $errores  = [];

        foreach ($asistentes as $i => $asistente) {
            $n   = $i + 1;
            $cip = $asistente->cip ?? 'SIN_CIP';
            $log[] = '';
            $log[] = "── [{$n}] CIP: {$cip} | Correo: {$asistente->correo}";

            $resultado = $this->enviarCorreo($asistente, $log);

            if ($resultado['success']) {
                $enviados++;
                $log[] = "   ✅ Enviado OK";
            } else {
                $log[] = "   ❌ Error: {$resultado['error']}";
                $errores[] = [
                    'cip'    => $cip,
                    'correo' => $asistente->correo,
                    'error'  => $resultado['error'],
                ];
            }
        }

        // ── 5. Resumen final ──────────────────────────────────────────────
        $log[] = '';
        $log[] = '════════════════════════════════════════';
        $log[] = "✅ Enviados : {$enviados}";
        $log[] = "❌ Errores  : " . count($errores);
        $log[] = '▶ FIN — ' . now()->toDateTimeString();
        $log[] = '════════════════════════════════════════';

        // Guardar en laravel.log también
        Log::info(implode("\n", $log));

        return response()->json([
            'mensaje'  => 'Proceso finalizado.',
            'enviados' => $enviados,
            'errores'  => $errores,
            'debug'    => $log,          // <-- visible en DevTools → Network → Response
        ]);
    }

    public function enviarPorCip(string $cip)
    {
        $log = [];
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
            $log[] = "   📎 Ruta tarjeta : {$rutaTarjeta}";
            $log[] = "   📎 Archivo existe: " . (file_exists($rutaTarjeta) ? '✅ SÍ' : '❌ NO');

            if (!file_exists($rutaTarjeta)) {
                return [
                    'success' => false,
                    'error'   => "Tarjeta no encontrada: {$rutaTarjeta}",
                ];
            }

            // 2. Leer y codificar
            $bytes = filesize($rutaTarjeta);
            $log[] = "   📎 Tamaño archivo: {$bytes} bytes";

            $tarjetaBase64  = base64_encode(file_get_contents($rutaTarjeta));
            $nombreCompleto = trim("{$asistente->nombres} {$asistente->apellidos}");
            $fromAddress    = config('mail.from.address', 'no-reply@tudominio.com');

            $log[] = "   📤 FROM   : {$fromAddress}";
            $log[] = "   📤 TO     : {$asistente->correo}";
            $log[] = "   📤 Subject: 🎟️ Tu Tarjeta Personal de Entrada — CIP CD La Libertad";

            // 3. Enviar
            $response = Resend::emails()->send([
                'from'    => $fromAddress,
                'to'      => [$asistente->correo],
                'subject' => '🎟️ Tu Tarjeta Personal de Entrada — CIP CD La Libertad',
                'html'    => $this->buildHtml($nombreCompleto, $asistente->cip, $asistente->capitulo),
                'attachments' => [
                    [
                        'filename'     => "tarjeta_{$asistente->cip}.png",
                        'content'      => $tarjetaBase64,
                        'content_type' => 'image/png',
                    ],
                ],
            ]);

            // Resend devuelve el ID del email si todo fue bien
            $emailId = $response->id ?? 'sin-id';
            $log[] = "   📨 Resend email ID: {$emailId}";

            return ['success' => true];

        } catch (\Throwable $e) {
            $log[] = "   ❌ EXCEPCIÓN: " . $e->getMessage();
            $log[] = "   ❌ En: " . $e->getFile() . ':' . $e->getLine();
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

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
          <meta charset="UTF-8"/>
          <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
          <title>Tarjeta de Entrada</title>
        </head>
        <body style="margin:0;padding:0;background:#f0f4f8;font-family:'Segoe UI',Arial,sans-serif;">

          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4f8;padding:30px 0;">
            <tr><td align="center">

              <table width="600" cellpadding="0" cellspacing="0"
                     style="background:#fff;border-radius:16px;overflow:hidden;
                            box-shadow:0 4px 24px rgba(0,0,0,.10);max-width:600px;">

                <tr>
                  <td style="background:linear-gradient(135deg,#1a3c6e 0%,#2563eb 100%);
                             padding:36px 40px;text-align:center;">
                    <div style="font-size:42px;margin-bottom:8px;">🏛️</div>
                    <h1 style="margin:0;color:#fff;font-size:22px;font-weight:700;letter-spacing:1px;">
                      COLEGIO DE INGENIEROS DEL PERÚ
                    </h1>
                    <p style="margin:6px 0 0;color:#bfdbfe;font-size:13px;letter-spacing:2px;">
                      CONSEJO DEPARTAMENTAL LA LIBERTAD
                    </p>
                  </td>
                </tr>

                <tr>
                  <td style="padding:38px 40px 28px;">
                    <p style="margin:0 0 4px;color:#6b7280;font-size:14px;">Estimado(a) colegiado(a),</p>
                    <h2 style="margin:0 0 24px;color:#1e40af;font-size:20px;font-weight:700;">{$nombre}</h2>

                    <table width="100%" cellpadding="0" cellspacing="0"
                           style="background:#eff6ff;border-left:4px solid #AD2B2E;border-radius:8px;margin-bottom:26px;">
                      <tr>
                        <td style="padding:18px 20px;">
                          <p style="margin:0;font-size:15px;color:#1e3a5f;line-height:1.65;">
                            ✅ &nbsp;<strong>Tu información ha sido validada.</strong><br/>
                            Esta es tu <strong>tarjeta personal de entrada</strong> al evento.<br/>
                            La encontrarás <strong>adjunta</strong> a este correo como archivo PNG.
                          </p>
                        </td>
                      </tr>
                    </table>

                    <table width="100%" cellpadding="0" cellspacing="0"
                           style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:26px;">
                      <tr style="background:#f8fafc;">
                        <td style="padding:13px 20px;border-bottom:1px solid #e5e7eb;">
                          <span style="font-size:12px;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;">👤 Nombre completo</span><br/>
                          <strong style="color:#111827;font-size:15px;">{$nombre}</strong>
                        </td>
                      </tr>
                      <tr>
                        <td style="padding:13px 20px;border-bottom:1px solid #e5e7eb;">
                          <span style="font-size:12px;color:#9ca3af;text-transform:uppercase;letter-spacing:1px;">🔢 CIP</span><br/>
                          <strong style="color:#111827;font-size:15px;">{$cip}</strong>
                        </td>
                      </tr>
                    </table>

                    <table width="100%" cellpadding="0" cellspacing="0"
                           style="background:#fef3c7;border:1px solid #fbbf24;border-radius:8px;margin-bottom:28px;">
                      <tr>
                        <td style="padding:16px 20px;text-align:center;">
                          <p style="margin:0;font-size:14px;color:#92400e;font-weight:700;">
                            🚫 Esta tarjeta es PERSONAL e INTRANSFERIBLE
                          </p>
                          <p style="margin:6px 0 0;font-size:13px;color:#b45309;">
                            Preséntala al ingresar junto con tu DNI. No se aceptarán transferencias.
                          </p>
                        </td>
                      </tr>
                    </table>

                    <p style="margin:0 0 12px;color:#374151;font-size:14px;font-weight:600;">📋 ¿Cómo usar tu tarjeta?</p>
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:10px;">
                      <tr><td style="padding:5px 0;font-size:14px;color:#4b5563;">1️⃣ &nbsp; Descarga el archivo PNG adjunto a este correo</td></tr>
                      <tr><td style="padding:5px 0;font-size:14px;color:#4b5563;">2️⃣ &nbsp; Muéstralo desde tu celular o imprímelo</td></tr>
                      <tr><td style="padding:5px 0;font-size:14px;color:#4b5563;">3️⃣ &nbsp; Preséntalo junto a tu DNI al personal de ingreso</td></tr>
                    </table>
                  </td>
                </tr>

                <tr>
                  <td style="background:#AD2B2E;padding:22px 40px;text-align:center;">
                    <p style="margin:0;color:#93c5fd;font-size:12px;line-height:1.6;">
                      © 2026 Colegio de Ingenieros del Perú — CD La Libertad<br/>
                      Este mensaje fue generado automáticamente, por favor no responder.
                    </p>
                  </td>
                </tr>

              </table>
            </td></tr>
          </table>

        </body>
        </html>
        HTML;
    }
}