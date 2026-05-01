<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CargueController;
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
        Route::get('/consultas/comentarios/{cedula}', [ConsultaController::class, 'comentariosPorCedula'])->name('consultas.comentarios');
        Route::post('/consultas/comentarios', [ConsultaController::class, 'crearComentario'])->name('consultas.comentarios.crear');
        Route::get('/consultas/telefonos/{cedula}', [ConsultaController::class, 'telefonosPorCedula'])->name('consultas.telefonos');
        Route::post('/consultas/telefonos', [ConsultaController::class, 'crearTelefono'])->name('consultas.telefonos.crear');
        Route::put('/consultas/telefonos/{tercero}', [ConsultaController::class, 'editarTelefono'])->name('consultas.telefonos.editar');
        Route::patch('/consultas/telefonos/{tercero}/notificar', [ConsultaController::class, 'toggleNotificar'])->name('consultas.telefonos.notificar');
    });

    // Sistemas EPS (solo admin@asesco.com)
    Route::middleware('superadmin')->group(function () {
        Route::get('/sistemas', [EpsSystemController::class, 'index'])->name('sistemas.index');
        Route::post('/sistemas', [EpsSystemController::class, 'store'])->name('sistemas.store');
        Route::put('/sistemas/{system}', [EpsSystemController::class, 'update'])->name('sistemas.update');
        Route::post('/sistemas/{system}/toggle', [EpsSystemController::class, 'toggle'])->name('sistemas.toggle');
        Route::post('/sistemas/{system}/test', [EpsSystemController::class, 'test'])->name('sistemas.test');
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

    // Cargues
    Route::middleware('permission:cargues.ver')->group(function () {
        Route::get('/cargues/telefonos', [CargueController::class, 'telefonos'])->name('cargues.telefonos');
        Route::get('/cargues/telefonos/listar', [CargueController::class, 'listar'])->name('cargues.telefonos.listar');
        Route::get('/cargues/comentarios', [CargueController::class, 'comentarios'])->name('cargues.comentarios');
        Route::get('/cargues/comentarios/listar', [CargueController::class, 'listarComentarios'])->name('cargues.comentarios.listar');
    });
    Route::middleware('permission:cargues.importar')->group(function () {
        Route::post('/cargues/telefonos/importar', [CargueController::class, 'importar'])->name('cargues.telefonos.importar');
        Route::post('/cargues/comentarios/importar', [CargueController::class, 'importarComentarios'])->name('cargues.comentarios.importar');
    });
});
