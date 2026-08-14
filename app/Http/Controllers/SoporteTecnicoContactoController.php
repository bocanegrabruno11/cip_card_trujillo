<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoporteTecnicoContacto;
use App\Models\ActividadUsuario;

class SoporteTecnicoContactoController extends Controller
{
    public function index()
    {
        ActividadUsuario::log('Accedió al listado de contactos de soporte', 'Admin - Soporte Técnico');
        $contactos = SoporteTecnicoContacto::orderBy('id', 'desc')->get();
        return view('Admin.soporte_tecnico_contactos.index', compact('contactos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'numero_contacto' => 'required_without:correo_electronico|nullable|string|max:50',
            'correo_electronico' => 'required_without:numero_contacto|nullable|email|max:255',
            'estado' => 'required|integer|in:0,1'
        ], [
            'numero_contacto.required_without' => 'Debes ingresar un número de contacto o un correo electrónico.',
            'correo_electronico.required_without' => 'Debes ingresar un correo electrónico o un número de contacto.',
        ]);

        SoporteTecnicoContacto::create($request->all());
        
        ActividadUsuario::log('Creó un nuevo contacto de soporte: ' . $request->nombres, 'Admin - Soporte Técnico');

        return redirect()->back()->with('success', 'Contacto agregado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'numero_contacto' => 'required_without:correo_electronico|nullable|string|max:50',
            'correo_electronico' => 'required_without:numero_contacto|nullable|email|max:255',
            'estado' => 'required|integer|in:0,1'
        ], [
            'numero_contacto.required_without' => 'Debes ingresar un número de contacto o un correo electrónico.',
            'correo_electronico.required_without' => 'Debes ingresar un correo electrónico o un número de contacto.',
        ]);

        $contacto = SoporteTecnicoContacto::findOrFail($id);
        $contacto->update($request->all());

        ActividadUsuario::log('Editó el contacto de soporte con ID ' . $id, 'Admin - Soporte Técnico');

        return redirect()->back()->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroy($id)
    {
        $contacto = SoporteTecnicoContacto::findOrFail($id);
        $contacto->delete();

        ActividadUsuario::log('Eliminó el contacto de soporte con ID ' . $id, 'Admin - Soporte Técnico');

        return redirect()->back()->with('success', 'Contacto eliminado correctamente.');
    }
}
