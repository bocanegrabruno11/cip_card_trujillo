<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'welcome'])->name('welcome');

Route::get('/mision-vision', [PageController::class, 'misionVision'])->name('mision-vision');

