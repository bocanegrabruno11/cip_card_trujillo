<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;

Route::get('/', [PageController::class, 'welcome'])->name('welcome');

Route::get('/mision-vision', [PageController::class, 'misionVision'])->name('mision-vision');
Route::get('/presentacion', [PageController::class, 'presentacion'])->name('presentacion');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
