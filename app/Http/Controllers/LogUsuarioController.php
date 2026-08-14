<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActividadUsuario;
use Carbon\Carbon;

class LogUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = ActividadUsuario::query();

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        if ($request->filled('rol')) {
            $query->where('user_role', $request->rol);
        }

        $roles = ActividadUsuario::select('user_role')->distinct()->pluck('user_role');

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);
        $logs->appends($request->all());

        return view('Admin.logs_usuarios.index', compact('logs', 'roles'));
    }

    public function exportarTxt(Request $request)
    {
        $query = ActividadUsuario::query();

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        
        if ($request->filled('rol')) {
            $query->where('user_role', $request->rol);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $contenido = "REGISTRO DE AUDITORIA DE USUARIOS (LOGS)\r\n";
        $contenido .= "Generado el: " . now()->format('d/m/Y H:i:s') . "\r\n";
        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $contenido .= "Rango de fechas: {$request->fecha_desde} al {$request->fecha_hasta}\r\n";
        }
        if ($request->filled('rol')) {
            $contenido .= "Filtro por Rol: {$request->rol}\r\n";
        }
        $contenido .= str_repeat("-", 100) . "\r\n\r\n";

        foreach ($logs as $log) {
            $fecha = $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : 'N/A';
            $contenido .= "[{$fecha}] Usuario: {$log->user_name} | Rol: {$log->user_role} | Módulo: {$log->view_accessed}\r\n";
            $contenido .= "Acción: {$log->action}\r\n";
            $contenido .= str_repeat("-", 80) . "\r\n";
        }

        $nombreArchivo = 'logs_usuarios_' . date('Ymd_His') . '.txt';

        return response($contenido)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', "attachment; filename=\"{$nombreArchivo}\"");
    }
}
