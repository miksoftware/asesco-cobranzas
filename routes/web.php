<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EpsSystemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Consultas
    Route::middleware('permission:consultas.ver')->group(function () {
        Route::get('/consultas', [ConsultaController::class, 'index'])->name('consultas.index');
        Route::post('/consultas/consultar', [ConsultaController::class, 'consultar'])->name('consultas.consultar');
        Route::get('/consultas/historial', [ConsultaController::class, 'historial'])->name('consultas.historial');
    });

    // Sistemas EPS
    Route::middleware('permission:sistemas.ver')->group(function () {
        Route::get('/sistemas', [EpsSystemController::class, 'index'])->name('sistemas.index');
    });
    Route::middleware('permission:sistemas.crear')->group(function () {
        Route::post('/sistemas', [EpsSystemController::class, 'store'])->name('sistemas.store');
    });
    Route::middleware('permission:sistemas.editar')->group(function () {
        Route::put('/sistemas/{system}', [EpsSystemController::class, 'update'])->name('sistemas.update');
        Route::post('/sistemas/{system}/toggle', [EpsSystemController::class, 'toggle'])->name('sistemas.toggle');
    });
    Route::middleware('permission:sistemas.eliminar')->group(function () {
        Route::delete('/sistemas/{system}', [EpsSystemController::class, 'destroy'])->name('sistemas.destroy');
    });

    // Usuarios
    Route::middleware('permission:usuarios.ver')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    });
    Route::middleware('permission:usuarios.crear')->group(function () {
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    });
    Route::middleware('permission:usuarios.editar')->group(function () {
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
    });
    Route::middleware('permission:usuarios.eliminar')->group(function () {
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });
});
