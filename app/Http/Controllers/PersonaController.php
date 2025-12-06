<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use Illuminate\Validation\Rule;
class PersonaController extends Controller
{
    // Mostrar lista de personas
    public function index()
    {
        $personas = Persona::with('user')->paginate(10);
        return view('persona.index', compact('personas'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('persona.create');
    }

    // Guardar registro
public function store(Request $request)
{
    $request->validate([
        'dni' => 'required|digits:8|unique:persona,dni', // ← Agrega unique
        'correo_contacto' => 'required|email',
        'direccion' => 'nullable|string|max:200',
        'celular' => 'nullable|digits:9',
    ]);

    Persona::create([
        'dni' => $request->dni,
        'correo_contacto' => $request->correo_contacto,
        'direccion' => $request->direccion,
        'celular' => $request->celular,
        'user_id' => auth()->id(),
    ]);

    return redirect()->back()->with('success', 'Información guardada correctamente.');
}


    // Mostrar detalle
    public function show($id)
    {
        $persona = Persona::with('user')->findOrFail($id);
        return view('persona.show', compact('persona'));
    }

    // Formulario para editar
    public function edit($id)
    {
        $persona = Persona::findOrFail($id);
        return view('persona.edit', compact('persona'));
    }



public function update(Request $request)
{
    $persona = Persona::where('user_id', auth()->id())->firstOrFail();

    $validated = $request->validate([
        'dni' => [
            'required',
            'numeric',
            'digits:8',
            Rule::unique('persona', 'dni')->ignore($persona->id_persona, 'id_persona')
        ],
        'correo_contacto' => 'required|email',
        'direccion' => 'required|string|max:200',
        'celular' => 'required|numeric|digits:9'
    ]);

    $persona->update($validated);

    return back()->with('success', 'Información actualizada correctamente');
}

    // Eliminar registro
    public function destroy($id)
    {
        $persona = Persona::findOrFail($id);
        $persona->delete();

        return redirect()->route('persona.index')->with('success', 'Persona eliminada correctamente.');
    }

public function actualizar()
{
    $userId = auth()->id();

    // Buscar persona por el user_id
    $persona = Persona::where('user_id', $userId)->first();

    return view('mesa-partes.actualizar', compact('persona')); // ← Cambia aquí
}

}
