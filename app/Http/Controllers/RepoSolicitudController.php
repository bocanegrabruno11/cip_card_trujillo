<?php

namespace App\Http\Controllers;

use App\Models\SolicitudRepositorio;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class RepoSolicitudController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validación de formato de campos
        $request->validate([
            'nombres' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'dni' => 'required|string|min:8|max:12', // Ajusté un poco para DNI/CE
            'foto_dni' => 'required|image|mimes:jpeg,png,jpg|max:5120', // Max 5MB
        ]);

        // 2. Verificar duplicidad de CORREO en solicitudes activas
        $existeEmail = SolicitudRepositorio::where('email', $request->email)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->exists();

        if ($existeEmail) {
            return back()
                ->withInput() // Mantiene lo que el usuario escribió
                ->with('warning', 'Ya se envió una petición con este correo.');
        }

        // 3. Verificar duplicidad de DNI en solicitudes activas
        $existeDni = SolicitudRepositorio::where('dni', $request->dni)
            ->whereIn('estado', ['pendiente', 'aprobado'])
            ->exists();

        if ($existeDni) {
            return back()
                ->withInput() // Mantiene lo que el usuario escribió
                ->with('warning', 'El DNI ingresado ya tiene una solicitud.');
        }

        try {
            // 4. Subir la foto
            $path = null;
            if ($request->hasFile('foto_dni')) {
                // Se guarda en storage/app/public/solicitudes_dni
                $path = $request->file('foto_dni')->store('solicitudes_dni', 'public');
            }

            // 5. Crear registro en BD
            SolicitudRepositorio::create([
                'nombres' => $request->nombres,
                'email' => $request->email,
                'dni' => $request->dni,
                'foto_dni_path' => $path,
                'estado' => 'pendiente',
            ]);

            return back()->with('success', 'Solicitud enviada correctamente.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Ocurrió un error al procesar su solicitud..');
        }
    }

    public function index(Request $request)
    {
        $query = SolicitudRepositorio::query();

        // Filtro por estado (Por defecto ver Pendientes)
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        } else {
            // Ordenar: primero pendientes, luego por fecha
            $query->orderByRaw("FIELD(estado, 'pendiente', 'aprobado', 'rechazado')");
        }

        // Búsqueda por DNI o Email
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('email', 'like', "%$s%")
                  ->orWhere('dni', 'like', "%$s%")
                  ->orWhere('nombres', 'like', "%$s%");
            });
        }

        $solicitudes = $query->orderBy('nombres')->latest()->paginate(10);

        return view('Admin.solicitudes-repo.index', compact('solicitudes'));
    }

    // Cambiar estado (Aprobar / Rechazar)
    public function updateState(Request $request, $id)
    {
        $solicitud = SolicitudRepositorio::findOrFail($id);

        $request->validate([
            'estado' => 'required|in:aprobado,rechazado,pendiente'
        ]);

        $solicitud->estado = $request->estado;
        $solicitud->fecha_respuesta = Carbon::now();
        $solicitud->save();

        $msg = $request->estado == 'aprobado' 
            ? 'Solicitud APROBADA. Recuerde agregar el correo manualmente en Google Drive.' 
            : 'Solicitud actualizada a ' . strtoupper($request->estado);

        return back()->with('success', $msg);
    }

    // Eliminar solicitud (Físico y BD)
    public function destroy($id)
    {
        $solicitud = SolicitudRepositorio::findOrFail($id);

        // Eliminar foto del storage para no llenar basura
        if ($solicitud->foto_dni_path && Storage::disk('public')->exists($solicitud->foto_dni_path)) {
            Storage::disk('public')->delete($solicitud->foto_dni_path);
        }

        $solicitud->delete();

        return back()->with('success', 'Solicitud eliminada correctamente.');
    }

    public function show($id)
    {
        $solicitud = SolicitudRepositorio::findOrFail($id);
        return view('Admin.solicitudes-repo.show', compact('solicitud'));
    }
}
