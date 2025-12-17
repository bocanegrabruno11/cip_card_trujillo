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

public function store(Request $request)
{
    $request->validate(
        [
            'dni' => 'required|digits:8|unique:persona,dni',
            'correo_contacto' => 'required|email',
            'direccion' => 'nullable|string|max:200',
            'celular' => 'nullable|digits:9',
        ],
        [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique' => 'El DNI ingresado ya se encuentra registrado.',
            'correo_contacto.required' => 'El correo de contacto es obligatorio.',
            'correo_contacto.email' => 'Debe ingresar un correo válido.',
            'direccion.max' => 'La dirección no debe superar los 200 caracteres.',
            'celular.digits' => 'El número de celular debe tener 9 dígitos.',
        ]
    );

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

    $validated = $request->validate(
        [
            'dni' => [
                'required',
                'numeric',
                'digits:8',
                Rule::unique('persona', 'dni')->ignore($persona->id_persona, 'id_persona')
            ],
            'correo_contacto' => 'required|email',
            'direccion' => 'required|string|max:200',
            'celular' => 'required|numeric|digits:9'
        ],
        [
            'dni.required' => 'El DNI es obligatorio.',
            'dni.numeric' => 'El DNI solo debe contener números.',
            'dni.digits' => 'El DNI debe tener exactamente 8 dígitos.',
            'dni.unique' => 'Este DNI ya está registrado por otro usuario.',
            'correo_contacto.required' => 'El correo de contacto es obligatorio.',
            'correo_contacto.email' => 'Debe ingresar un correo electrónico válido.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección no debe superar los 200 caracteres.',
            'celular.required' => 'El celular es obligatorio.',
            'celular.numeric' => 'El celular solo debe contener números.',
            'celular.digits' => 'El celular debe tener exactamente 9 dígitos.',
        ]
    );

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
public function buscarPorUsuario(Request $request)
{
    try {
        $userId = auth()->id();
        
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }
        
        $persona = Persona::where('user_id', $userId)->first();
        
        if ($persona) {
            return response()->json([
                'success' => true,
                'persona' => [
                    'id_persona' => $persona->id_persona,
                    'dni' => $persona->dni,
                    'correo_contacto' => $persona->correo_contacto,
                    'direccion' => $persona->direccion,
                    'celular' => $persona->celular
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No tienes tu información actualizada',
                'redirect_url' => route('persona.actualizar')
            ], 404);
        }
        
    } catch (\Exception $e) {
        \Log::error('Error en buscarPorUsuario: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al buscar información: ' . $e->getMessage()
        ], 500);
    }
}
}
