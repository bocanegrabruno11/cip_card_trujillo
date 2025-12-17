<?php

namespace App\Http\Controllers;

use App\Models\ProcesoJrd;
use App\Models\Jrd;

class JrdProcesoController extends Controller
{
    public function pasarSiguienteProceso($id_jrd)
    {
        // 0️⃣ Validar JRD
        $jrd = Jrd::find($id_jrd);

        if (!$jrd) {
            return back()->with('error', 'JRD no encontrado.');
        }

        if ($jrd->estado === 'terminado') {
            return back()->with('error', 'El JRD ya se encuentra finalizado.');
        }

        // 1️⃣ Obtener proceso activo
        $procesoActual = ProcesoJrd::where('jrd_id', $id_jrd)
            ->whereNotIn('estado', ['Finalizado', 'Terminado'])
            ->orderBy('fecha', 'desc')
            ->first();

        if (!$procesoActual) {
            return back()->with('error', 'No hay proceso activo.');
        }

        // 2️⃣ Finalizar proceso actual
        $procesoActual->estado = 'Finalizado';
        $procesoActual->save();

        // 3️⃣ Flujo de procesos - AJUSTA ESTOS PROCESOS SEGÚN TU FLUJO DE JRD
        if ($procesoActual->nombre === 'Validacion de Voucher') {

            // 👉 JRD pasa a estado "en proceso"
            $jrd->estado = 'en proceso';
            $jrd->save();

            ProcesoJrd::create([
                'jrd_id'      => $id_jrd,
                'fecha'       => now(),
                'nombre'      => 'Revisión Documentaria',
                'descripcion' => 'Revisión de toda la documentación presentada y verificación de requisitos.',
                'estado'      => 'Activo'
            ]);

        } 
        elseif ($procesoActual->nombre === 'Revisión Documentaria') {

            // 👉 JRD continúa en proceso
            $jrd->estado = 'en proceso';
            $jrd->save();

            ProcesoJrd::create([
                'jrd_id'      => $id_jrd,
                'fecha'       => now(),
                'nombre'      => 'Audiencia Inicial',
                'descripcion' => 'Convocatoria a las partes para audiencia inicial.',
                'estado'      => 'Activo'
            ]);

        } 
        elseif ($procesoActual->nombre === 'Audiencia Inicial') {

            // 👉 JRD continúa en proceso
            $jrd->estado = 'en proceso';
            $jrd->save();

            ProcesoJrd::create([
                'jrd_id'      => $id_jrd,
                'fecha'       => now(),
                'nombre'      => 'Sustanciación del Proceso',
                'descripcion' => 'Desarrollo de las audiencias y presentación de pruebas.',
                'estado'      => 'Activo'
            ]);

        } 
        elseif ($procesoActual->nombre === 'Sustanciación del Proceso') {

            // 👉 JRD continúa en proceso
            $jrd->estado = 'en proceso';
            $jrd->save();

            ProcesoJrd::create([
                'jrd_id'      => $id_jrd,
                'fecha'       => now(),
                'nombre'      => 'Emisión de Resolución',
                'descripcion' => 'Elaboración y emisión de la resolución final.',
                'estado'      => 'Activo'
            ]);

        } 
        elseif ($procesoActual->nombre === 'Emisión de Resolución') {

            // 👉 Finalizar JRD
            $jrd->estado = 'terminado';
            $jrd->fecha_finalizacion = now();
            $jrd->save();

            return back()->with('success', 'JRD finalizado correctamente.');
        }

        return back()->with('success', 'Proceso actualizado correctamente.');
    }

    /**
     * OPCIONAL: Método para crear un proceso personalizado
     */
    public function crearProceso(Request $request, $id_jrd)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'estado' => 'required|string|max:150'
        ]);

        $jrd = Jrd::find($id_jrd);
        
        if (!$jrd) {
            return back()->with('error', 'JRD no encontrado.');
        }

        ProcesoJrd::create([
            'jrd_id'      => $id_jrd,
            'fecha'       => now(),
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'estado'      => $request->estado
        ]);

        return back()->with('success', 'Proceso creado correctamente.');
    }

    /**
     * OPCIONAL: Método para actualizar estado de un proceso específico
     */
    public function actualizarEstadoProceso(Request $request, $id_jrd, $id_proceso)
    {
        $request->validate([
            'estado' => 'required|string|max:150'
        ]);

        $proceso = ProcesoJrd::where('jrd_id', $id_jrd)
            ->where('id_proceso_jrd', $id_proceso)
            ->first();

        if (!$proceso) {
            return back()->with('error', 'Proceso no encontrado.');
        }

        $proceso->estado = $request->estado;
        $proceso->save();

        return back()->with('success', 'Estado del proceso actualizado correctamente.');
    }
}