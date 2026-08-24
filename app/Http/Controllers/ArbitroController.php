<?php

namespace App\Http\Controllers;

use App\Models\Arbitro;
use App\Models\User;
use App\Models\ArbitroUser;
use App\Models\ProcesoArbitrajePersona;
use App\Models\Arbitraje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;

class ArbitroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $arbitros = Arbitro::with('users')->paginate(10);
        return view('Admin.arbitros.index', compact('arbitros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Admin.arbitros.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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
            'nombre.required' => 'El nombre del árbitro es obligatorio.',
            'apellidos.required' => 'Los apellidos del árbitro son obligatorios.',
            'correo.email' => 'El correo del árbitro debe ser un email válido.',
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

            $arbitro = Arbitro::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'dni' => $request->dni,
                'ruc' => $request->ruc,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
            ]);

            ArbitroUser::create([
                'arbitro_id' => $arbitro->id,
                'user_id' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('arbitros.index')
                ->with('success', 'Árbitro y usuario creados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el árbitro: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
/**
 * Display the specified resource.
 */
public function show($id)
{
    $arbitro = Arbitro::with('users')->findOrFail($id);

    // Obtener los casos donde este árbitro está vinculado
    $casosVinculados = ProcesoArbitrajePersona::where('dni', $arbitro->dni)
        ->where('tipo', 'Arbitro')
        ->with(['arbitraje' => function($query) {
            $query->with(['user.persona', 'personas']);
        }])
        ->orderBy('id_proceso_arbitraje_persona', 'desc')  // ✅ CAMBIADO
        ->get();

    return view('Admin.arbitros.show', compact('arbitro', 'casosVinculados'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Arbitro $arbitro)
    {
        $arbitro->load('users');
        $usuario = $arbitro->users->first();
        return view('Admin.arbitros.edit', compact('arbitro', 'usuario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Arbitro $arbitro)
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

            $arbitro->update($request->only([
                'nombre', 'apellidos', 'dni', 'ruc', 
                'telefono', 'correo', 'direccion'
            ]));

            $usuario = $arbitro->users->first();
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

            return redirect()->route('arbitros.index')
                ->with('success', 'Árbitro actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el árbitro: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Arbitro $arbitro)
    {
        try {
            DB::beginTransaction();

            $usuario = $arbitro->users->first();
            $arbitro->users()->detach();
            $arbitro->delete();
            
            if ($usuario) {
                $usuario->delete();
            }

            DB::commit();

            return redirect()->route('arbitros.index')
                ->with('success', 'Árbitro y usuario eliminados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar el árbitro: ' . $e->getMessage());
        }
    }
}