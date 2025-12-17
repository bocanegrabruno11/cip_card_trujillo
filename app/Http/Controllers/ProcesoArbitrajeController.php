<?php

namespace App\Http\Controllers;

use App\Models\ProcesoArbitraje;
use App\Models\Arbitraje;

class ProcesoArbitrajeController extends Controller
{
    public function pasarSiguienteProceso($id_arbitraje)
    {
        // 0️⃣ Validar arbitraje
        $arbitraje = Arbitraje::find($id_arbitraje);

        if (!$arbitraje) {
            return back()->with('error', 'Arbitraje no encontrado.');
        }

        if ($arbitraje->estado === 'terminado') {
            return back()->with('error', 'El arbitraje ya se encuentra finalizado.');
        }

        // 1️⃣ Obtener proceso activo
        $procesoActual = ProcesoArbitraje::where('arbitraje_id', $id_arbitraje)
            ->where('estado', '!=', 'Finalizado')
            ->orderBy('fecha', 'desc')
            ->first();

        if (!$procesoActual) {
            return back()->with('error', 'No hay proceso activo.');
        }

        // 2️⃣ Finalizar proceso actual
        $procesoActual->estado = 'Finalizado';
        $procesoActual->save();

        // 3️⃣ Flujo de procesos
        if ($procesoActual->nombre === 'Seleccion de un Arbitro') {

            // 👉 Arbitraje en proceso
            $arbitraje->estado = 'en proceso';
            $arbitraje->save();

            ProcesoArbitraje::create([
                'arbitraje_id' => $id_arbitraje,
                'fecha'        => now(),
                'nombre'       => 'Reunion de Asignacion',
                'descripcion'  => 'Reunión para: asignación de proyecto de reglas de arbitraje, definición de los costos administrativos y honorarios del árbitro.',
                'estado'       => 'Activo'
            ]);

        } 
        elseif ($procesoActual->nombre === 'Reunion de Asignacion') {

            // 👉 Arbitraje en proceso
            $arbitraje->estado = 'en proceso';
            $arbitraje->save();

            ProcesoArbitraje::create([
                'arbitraje_id' => $id_arbitraje,
                'fecha'        => now(),
                'nombre'       => 'Realizar pagos de arbitraje',
                'descripcion'  => 'Se registrarán los pagos de ambas partes.',
                'estado'       => 'Activo'
            ]);

        } 
        elseif ($procesoActual->nombre === 'Realizar pagos de arbitraje') {

            // 👉 Finalizar arbitraje
            $arbitraje->estado = 'terminado';
            $arbitraje->fecha_finalizacion = now();
            $arbitraje->save();

            return back()->with('success', 'Arbitraje finalizado correctamente.');
        }

        return back()->with('success', 'Proceso actualizado correctamente.');
    }
}
