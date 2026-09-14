<?php

namespace App\Http\Controllers;

use App\Models\Adjudicador;
use App\Models\User;
use App\Models\AdjudicadorUser;
use App\Models\ProcesoJrdPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AdjudicadorController extends Controller
{
    public function index()
    {
        $adjudicadores = Adjudicador::with('users')->paginate(10);
        return view('Admin.adjudicadores.index', compact('adjudicadores'));
    }

    public function create()
    {
        return view('Admin.adjudicadores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'dni' => ['nullable', 'string', 'max:20'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nombre.required' => 'El nombre del adjudicador es obligatorio.',
            'apellidos.required' => 'Los apellidos del adjudicador son obligatorios.',
            'correo.email' => 'El correo del adjudicador debe ser un email válido.',
            'name.required' => 'El nombre de usuario es obligatorio.',
            'email.required' => 'El email de usuario es obligatorio.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'activo' => 1
            ]);

            $user->assignRole(1);

            $adjudicador = Adjudicador::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'dni' => $request->dni,
                'ruc' => $request->ruc,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
            ]);

            AdjudicadorUser::create([
                'adjudicador_id' => $adjudicador->id,
                'user_id' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('adjudicadores.index')
                ->with('success', 'Adjudicador y usuario creados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el adjudicador: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $adjudicador = Adjudicador::with('users')->findOrFail($id);

        $casosVinculados = ProcesoJrdPersona::where('dni', $adjudicador->dni)
            ->where('tipo', 'Adjudicador')
            ->with(['jrd' => function($query) {
                $query->with(['user.persona', 'personas']);
            }])
            ->orderBy('id_proceso_jrd_persona', 'desc')
            ->get();

        return view('Admin.adjudicadores.show', compact('adjudicador', 'casosVinculados'));
    }

    public function edit(Adjudicador $adjudicador)
    {
        $adjudicador->load('users');
        $usuario = $adjudicador->users->first();
        return view('Admin.adjudicadores.edit', compact('adjudicador', 'usuario'));
    }

    public function update(Request $request, Adjudicador $adjudicador)
    {
        $usuario = $adjudicador->users->first();

        // Reglas base para el adjudicador
        $rules = [
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'dni' => ['nullable', 'string', 'max:20'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string'],
        ];

        // Reglas adicionales si existe un usuario asociado
        if ($usuario) {
            $rules['name'] = ['nullable', 'string', 'max:255'];
            $rules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($usuario->id),
            ];
            $rules['password'] = ['nullable', 'confirmed', Rules\Password::defaults()];
        }

        $messages = [
            'email.required' => 'El email de usuario es obligatorio.',
            'email.email' => 'Debe ingresar un email válido.',
            'email.unique' => 'Este email ya está registrado por otro usuario.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        ];

        $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            // 1. Actualizar datos del adjudicador
            $adjudicador->update($request->only([
                'nombre', 'apellidos', 'dni', 'ruc',
                'telefono', 'correo', 'direccion'
            ]));

            // 2. Actualizar datos del usuario asociado
            if ($usuario) {
                $usuario->name = $request->filled('name') ? $request->name : $usuario->name;

                // ✅ Permitir cambio de email
                if ($request->filled('email')) {
                    $usuario->email = $request->email;
                }

                // ✅ Cambiar contraseña solo si se ingresó una nueva
                if ($request->filled('password')) {
                    $usuario->password = Hash::make($request->password);
                }

                $usuario->save();
            }

            DB::commit();

            return redirect()->route('adjudicadores.index')
                ->with('success', 'Adjudicador actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el adjudicador: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Adjudicador $adjudicador)
    {
        try {
            DB::beginTransaction();

            $usuario = $adjudicador->users->first();
            $adjudicador->users()->detach();
            $adjudicador->delete();

            if ($usuario) {
                $usuario->delete();
            }

            DB::commit();

            return redirect()->route('adjudicadores.index')
                ->with('success', 'Adjudicador y usuario eliminados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el adjudicador: ' . $e->getMessage());
        }
    }
}