<?php

namespace App\Http\Controllers;

use App\Models\ProcesoArbitraje;
use App\Models\ProcesoArbitrajeDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProcesoArbitrajeDocumentoController extends Controller
{
    public function store(Request $request, $id_arbitraje)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'proceso_id' => 'required|exists:procesos_arbitraje,id_proceso_arbitraje'
        ]);

        // 1️⃣ Obtener el proceso específico
        $proceso = ProcesoArbitraje::find($request->proceso_id);
        
        if (!$proceso || $proceso->arbitraje_id != $id_arbitraje) {
            return back()->with('error', 'Proceso no válido para este arbitraje.');
        }

        // 2️⃣ Guardar archivo en storage/app/public/uploads/vouchers
        $file = $request->file('archivo');
        
        // Crear nombre único
        $filename = time() . '_' . $file->getClientOriginalName();
        
        // Guardar usando storeAs
        $relativePath = $file->storeAs('uploads/vouchers', $filename, 'public');
        
        // Crear la ruta completa CON /storage/ al inicio
        $fullPath = '/storage/' . $relativePath;

        // 3️⃣ Determinar tipo de documento
        $extension = strtolower($file->getClientOriginalExtension());
        $tipoDocumento = ($extension === 'pdf') ? 'pdf' : 'imagen';

        // 4️⃣ Registrar documento CON la ruta completa que incluye /storage/
        ProcesoArbitrajeDocumento::create([
            'proceso_arbitraje_id' => $proceso->id_proceso_arbitraje,
            'fecha_subida'        => now(),
            'tipo_documento'      => $tipoDocumento,
            'nombre_original'     => $file->getClientOriginalName(),
            'ruta_archivo'        => $fullPath, // "/storage/uploads/vouchers/1765993567_nombre.pdf"
        ]);

        return back()->with('success', 'Documento registrado correctamente.');
    }
}