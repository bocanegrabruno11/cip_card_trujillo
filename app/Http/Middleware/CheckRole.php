<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Si no está logueado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Validar rol directamente desde la tabla roles
        if (!auth()->user()->roles()->where('name', $role)->exists()) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
