<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    public function mostrar($filename)
    {
        try {
            // Verificar autenticación y permisos
            if (!Auth::check()) {
                abort(403, 'No autorizado');
            }
            
            $path = 'uploads/vouchers/' . $filename;
            
            // Verificar si el archivo existe
            if (!Storage::disk('public')->exists($path)) {
                abort(404, 'Archivo no encontrado');
            }
            
            // Obtener el archivo
            $file = Storage::disk('public')->get($path);
            $mimeType = Storage::disk('public')->mimeType($path);
            
            return response($file, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
                
        } catch (\Exception $e) {
            Log::error('Error al mostrar documento', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            abort(404, 'Error al cargar el archivo');
        }
    }
    
    public function descargar($filename)
    {
        try {
            // Verificar autenticación y permisos
            if (!Auth::check()) {
                abort(403, 'No autorizado');
            }
            
            $path = 'uploads/vouchers/' . $filename;
            
            // Verificar si el archivo existe
            if (!Storage::disk('public')->exists($path)) {
                abort(404, 'Archivo no encontrado');
            }
            
            return Storage::disk('public')->download($path, $filename);
                
        } catch (\Exception $e) {
            Log::error('Error al descargar documento', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            abort(404, 'Error al descargar el archivo');
        }
    }
}