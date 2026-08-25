<?php

namespace App\Http\Controllers;

use App\Models\Adjudicador;
use App\Models\User;
use App\Models\AdjudicadorUser;
use App\Models\ProcesoJrdPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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

        // Obtener los casos JRD donde este adjudicador está vinculado
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
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'apellidos' => ['required', 'string', 'max:255'],
            'dni' => ['nullable', 'string', 'max:20'],
            'ruc' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255'],
            'direccion' => ['nullable', 'string'],
        ]);

        try {
            DB::beginTransaction();

            $adjudicador->update($request->only([
                'nombre', 'apellidos', 'dni', 'ruc', 
                'telefono', 'correo', 'direccion'
            ]));

            $usuario = $adjudicador->users->first();
            if ($usuario) {
                $usuario->update([
                    'name' => $request->name ?? $usuario->name,
                ]);
                
                if ($request->filled('password')) {
                    $request->validate([
                        'password' => ['required', 'confirmed', Rules\Password::defaults()],
                    ]);
                    $usuario->update([
                        'password' => Hash::make($request->password),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('adjudicadores.index')
                ->with('success', 'Adjudicador actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el adjudicador: ' . $e->getMessage());
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