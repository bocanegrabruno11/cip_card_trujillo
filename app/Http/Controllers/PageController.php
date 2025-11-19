<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function misionVision()
    {
        return view('contenido.mision-vision');
    }

    public function welcome()
    {
        return view('welcome');
    }
}
