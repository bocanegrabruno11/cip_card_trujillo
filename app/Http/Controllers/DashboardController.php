<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Arbitraje;
use App\Models\Jrd;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;
        $userDni = $user->persona->dni ?? null;

        /* =========================
           ARBITRAJES DEL USUARIO
        ==========================*/

        $arbitrajes = Arbitraje::where(function ($query) use ($userId, $userDni) {
                $query->where('user_id', $userId);

                if ($userDni) {
                    $query->orWhereHas('personas', function ($q) use ($userDni) {
                        $q->where('dni', $userDni);
                    });
                }
            })->get();

        $arbitrajesRecibidos   = $arbitrajes->count();
        $arbitrajesPendientes  = $arbitrajes->where('estado', 'Pendiente')->count();
        $arbitrajesRevision    = $arbitrajes->where('estado', 'En revisión')->count();
        $arbitrajesConcluidos  = $arbitrajes->where('estado', 'Concluido')->count();

        /* =========================
           JRD DEL USUARIO
        ==========================*/

        $jrds = Jrd::where(function ($query) use ($userId, $userDni) {
                $query->where('user_id', $userId);

                if ($userDni) {
                    $query->orWhereHas('personas', function ($q) use ($userDni) {
                        $q->where('dni', $userDni);
                    });
                }
            })->get();

        $jrdPendientes = $jrds->where('estado', 'Pendiente')->count();
        $jrdRevision   = $jrds->where('estado', 'En revisión')->count();
        $jrdConcluidos = $jrds->where('estado', 'Concluido')->count();

        /* =========================
           VISTA
        ==========================*/

        return view('mesa-partes.dashboard', compact(
            'arbitrajesRecibidos',
            'arbitrajesPendientes',
            'arbitrajesRevision',
            'arbitrajesConcluidos',
            'jrdPendientes',
            'jrdRevision',
            'jrdConcluidos'
        ));
    }
}
