<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function misionVision()
    {
        return view('contenido.mision-vision');
    }

    public function welcome()
    {
        $popupData = Publicacion::with('detalles')
        ->where('seccion', 'inicio_popup')
        ->where('activo', true)
        ->where('activo', 1)
        ->latest()
        ->first();

        // 2. SLIDER PRINCIPAL (Nueva consulta)
        // Buscamos la publicación más reciente de 'inicio_slider'
        $sliderData = Publicacion::with('detalles')
            ->where('seccion', 'inicio_slider')
            ->where('activo', 1)
            ->latest()
            ->first();

        // Pasamos ambas variables
        return view('welcome', compact('popupData', 'sliderData'));
    }

    public function presentacion()
    {
        // 1. Buscamos LA ÚLTIMA publicación registrada para la sección 'presentacion'
        $ultimaPublicacion = Publicacion::with('detalles')
            ->where('seccion', 'presentacion')
            ->where('activo', true)
            ->latest() // Ordena por created_at desc
            ->first();

        // 2. Inicializamos variables vacías por si no hay registros aún
        $imagenPrincipal = null;
        $galeria = collect([]); 

        // 3. Si existe la publicación, separamos las imágenes
        if ($ultimaPublicacion) {
            // Buscamos en la relación 'detalles' la que tenga grupo 'principal'
            $imagenPrincipal = $ultimaPublicacion->detalles->where('grupo', 'principal')->first();
            
            // Buscamos las que tengan grupo 'galeria'
            $galeria = $ultimaPublicacion->detalles->where('grupo', 'galeria');
        }

        return view('contenido.presentacion', compact('imagenPrincipal', 'galeria'));
    }

    public function gestionContenido()
    {
        return view('gestion-contenido.main');
    }
}
