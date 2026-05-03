<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MesaVirtualController extends Controller
{
    public function index()
    {
        return view('mesa-partes.mesa-virtual');
    }
}