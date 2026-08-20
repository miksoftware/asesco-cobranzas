<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CargueController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\EpsSystemController;
use App\Http\Controllers\RolController;
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
        Route::get('/consultas/retenciones/{cedula}', [ConsultaController::class, 'retencionesPorCedula'])->name('consultas.retenciones');
        Route::post('/consultas/telefonos', [ConsultaController::class, 'crearTelefono'])->name('consultas.telefonos.crear');
        Route::put('/consultas/telefonos/{tercero}', [ConsultaController::class, 'editarTelefono'])->name('consultas.telefonos.editar');
        Route::patch('/consultas/telefonos/{tercero}/notificar', [ConsultaController::class, 'toggleNotificar'])->name('consultas.telefonos.notificar');
        Route::get('/consultas/adjuntos/{cedula}', [ConsultaController::class, 'adjuntosPorCedula'])->name('consultas.adjuntos.list');
        Route::post('/consultas/adjuntos', [ConsultaController::class, 'uploadAdjunto'])->name('consultas.adjuntos.upload');
        Route::delete('/consultas/adjuntos/{adjunto}', [ConsultaController::class, 'deleteAdjunto'])->name('consultas.adjuntos.delete');
        Route::get('/consultas/pagos/{cedula}', [ConsultaController::class, 'pagosPorCedula'])->name('consultas.pagos.list');
        Route::post('/consultas/pagos', [ConsultaController::class, 'savePago'])->name('consultas.pagos.store');
        Route::post('/consultas/pagos/soporte', [ConsultaController::class, 'uploadPagoSoporte'])->name('consultas.pagos.soporte');
        Route::delete('/consultas/pagos/{pago}', [ConsultaController::class, 'deletePago'])->name('consultas.pagos.delete');
    });

    // Sistemas EPS (solo admin@asesco.com)
    Route::middleware('superadmin')->group(function () {
        Route::get('/sistemas', [EpsSystemController::class, 'index'])->name('sistemas.index');
        Route::post('/sistemas', [EpsSystemController::class, 'store'])->name('sistemas.store');
        Route::put('/sistemas/{system}', [EpsSystemController::class, 'update'])->name('sistemas.update');
        Route::post('/sistemas/{system}/toggle', [EpsSystemController::class, 'toggle'])->name('sistemas.toggle');
        Route::post('/sistemas/{system}/test', [EpsSystemController::class, 'test'])->name('sistemas.test');
        Route::delete('/sistemas/{system}', [EpsSystemController::class, 'destroy'])->name('sistemas.destroy');

        // Borrar cargue de comentarios (solo superadmin)
        Route::delete('/cargues/comentarios/borrar', [CargueController::class, 'borrarCargueComentarios'])->name('cargues.comentarios.borrar');

        // Gestión de roles y permisos
        Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RolController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RolController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RolController::class, 'destroy'])->name('roles.destroy');
    });

    // Usuarios
    Route::middleware('permission:usuarios.ver')->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
    });

    // Retenciones
    Route::get('/retenciones', [\App\Http\Controllers\RetencionController::class, 'index'])->name('retenciones.index');
    Route::get('/retenciones/listado', [\App\Http\Controllers\RetencionController::class, 'list'])->name('retenciones.list');
    Route::get('/retenciones/{retencion}', [\App\Http\Controllers\RetencionController::class, 'show'])->name('retenciones.show');
    Route::get('/retenciones/{retencion}/historial', [\App\Http\Controllers\RetencionController::class, 'historial'])->name('retenciones.historial');
    Route::post('/retenciones/section1', [\App\Http\Controllers\RetencionController::class, 'saveSection1'])->name('retenciones.saveSection1');
    Route::post('/retenciones/section2', [\App\Http\Controllers\RetencionController::class, 'saveSection2'])->name('retenciones.saveSection2');
    Route::post('/retenciones/abonos', [\App\Http\Controllers\RetencionController::class, 'saveAbonos'])->name('retenciones.saveAbonos');
    Route::post('/retenciones/abonos/soporte', [\App\Http\Controllers\RetencionController::class, 'uploadAbonoSoporte'])->name('retenciones.uploadAbonoSoporte');
    Route::post('/retenciones/gestiones', [\App\Http\Controllers\RetencionController::class, 'saveGestion'])->name('retenciones.saveGestion');
    Route::post('/retenciones/{retencion}/unlock', [\App\Http\Controllers\RetencionController::class, 'unlockSection'])->name('retenciones.unlock');
    Route::middleware('permission:usuarios.crear')->group(function () {
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
    });
    Route::middleware('permission:usuarios.editar')->group(function () {
        Route::put('/usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
    });
    Route::middleware('permission:usuarios.eliminar')->group(function () {
        Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])->name('usuarios.destroy');
    });

    // Empresas
    Route::middleware('permission:empresas.crear')->group(function () {
        Route::get('/empresas/crear', [EmpresaController::class, 'create'])->name('empresas.create');
        Route::post('/empresas', [EmpresaController::class, 'store'])->name('empresas.store');
    });
    Route::middleware('permission:empresas.ver')->group(function () {
        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::get('/empresas/{empresa}', [EmpresaController::class, 'show'])->name('empresas.show');
    });
    Route::middleware('permission:empresas.editar')->group(function () {
        Route::get('/empresas/{empresa}/editar', [EmpresaController::class, 'edit'])->name('empresas.edit');
        Route::patch('/empresas/{empresa}/nombre', [EmpresaController::class, 'updateNombre'])->name('empresas.updateNombre');
        Route::post('/empresas/{empresa}/tarifas', [EmpresaController::class, 'saveTarifas'])->name('empresas.saveTarifas');
        Route::post('/empresas/{empresa}/canales', [EmpresaController::class, 'saveCanales'])->name('empresas.saveCanales');
        Route::post('/empresas/{empresa}/lineamientos', [EmpresaController::class, 'saveLineamientos'])->name('empresas.saveLineamientos');
        Route::post('/empresas/{empresa}/toggle', [EmpresaController::class, 'toggle'])->name('empresas.toggle');
        Route::post('/empresas/{empresa}/lineamientos/{lineamiento}/activar', [EmpresaController::class, 'activarLineamiento'])->name('empresas.lineamientos.activar');
    });
    Route::middleware('permission:empresas.eliminar')->group(function () {
        Route::delete('/empresas/{empresa}', [EmpresaController::class, 'destroy'])->name('empresas.destroy');
    });

    // Cargues
    Route::middleware('permission:cargues.ver')->group(function () {
        Route::get('/cargues/telefonos', [CargueController::class, 'telefonos'])->name('cargues.telefonos');
        Route::get('/cargues/telefonos/listar', [CargueController::class, 'listar'])->name('cargues.telefonos.listar');
        Route::get('/cargues/telefonos/plantilla', [CargueController::class, 'descargarPlantilla'])->name('cargues.telefonos.plantilla');
        Route::get('/cargues/comentarios', [CargueController::class, 'comentarios'])->name('cargues.comentarios');
        Route::get('/cargues/comentarios/listar', [CargueController::class, 'listarComentarios'])->name('cargues.comentarios.listar');
    });
    Route::middleware('permission:cargues.importar')->group(function () {
        Route::post('/cargues/telefonos/validar', [CargueController::class, 'validarTerceros'])->name('cargues.telefonos.validar');
        Route::post('/cargues/telefonos/importar', [CargueController::class, 'importar'])->name('cargues.telefonos.importar');
        Route::post('/cargues/comentarios/importar', [CargueController::class, 'importarComentarios'])->name('cargues.comentarios.importar');
    });
});
