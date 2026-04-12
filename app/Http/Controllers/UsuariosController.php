<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Persona;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['persona', 'roles'])->where('activo', '=', 1);

        // Filtro por Rol
        if ($request->filled('rol')) {
            $query->role($request->rol);
        }

        // Filtro por Nombre de usuario o DNI de persona
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('persona', function($q2) use ($search) {
                      $q2->where('dni', 'LIKE', "%{$search}%");
                  });
            });
        }

        $usuarios = $query->paginate(10);
        $roles = Role::all();

        return view('Admin.usuarios.index', compact('usuarios', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('Admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'rol' => 'required|exists:roles,name',
            'dni' => 'required|string|max:15|unique:persona,dni'
        ]);

        // Crear Usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar Rol
        $user->assignRole($request->rol);

        // Crear la Persona vinculada
        Persona::create([
            'user_id' => $user->id,
            'dni' => $request->dni,
            'correo_contacto' => $request->email,
        ]);

        return redirect()->route('admin-usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($id)
    {
        $usuario = User::with('persona')->findOrFail($id);
        $roles = Role::all();
        return view('Admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $usuario->id,
            'rol' => 'required|exists:roles,name',
            'password' => 'nullable|string|min:8', // Opcional, solo si quiere cambiarla
        ]);

        // Actualizar datos básicos
        $usuario->email = $request->email;
        
        // Actualizar contraseña solo si se ingresó una nueva
        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        $usuario->save();

        // Actualizar rol
        $usuario->syncRoles([$request->rol]);

        return redirect()->route('admin-usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['activo' => 0]); 

        return redirect()->route('admin-usuarios.index')->with('success', 'Usuario desactivado correctamente.');
    }
}