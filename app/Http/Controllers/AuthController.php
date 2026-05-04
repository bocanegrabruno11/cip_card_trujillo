<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsuarioEvento;
use Illuminate\Support\Facades\Hash; // 👈 FALTABA ESTO

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $usuario = UsuarioEvento::where('usuario', $request->usuario)->first();

        if ($usuario && Hash::check($request->password, $usuario->password)) {
            session(['usuario' => $usuario->usuario]);
            return redirect('/dashboard-eventos');
        }

        return back()->with('error', 'Credenciales incorrectas');
    }

    public function logout()
    {
        session()->forget('usuario');
        return redirect('/login-eventos'); // 👈 mejor redirigir al login correcto
    }
}