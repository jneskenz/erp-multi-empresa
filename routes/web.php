<?php

/*
|--------------------------------------------------------------------------
| Directorios de rutas por apps: Workspace, CRM, ERP, etc.
| Models: App\Models\Workspace\Empresa, App\Models\Workspace\Sede, etc.
| Middleware: grupo.access, empresa.access, rol.administrador
| Controllers: App\Http\Controllers\Workspace\Grupo\..., App\Http\Controllers\Workspace\Empresa\..
| Views: resources\views\apps\workspace\empresa\..., resources\views\apps\workspace\grupo\...
| Views: resources\views\apps\erp\dashboard\..., resources\views\apps\erp\ventas\...
|--------------------------------------------------------------------------
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\GrupoEmpresaController as AdminGrupoController;
use App\Http\Controllers\Admin\LeadClienteController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Workspace\GrupoDashboardController;
use App\Http\Controllers\Workspace\EmpresaController;
use App\Http\Controllers\Workspace\SedeController;
use App\Http\Controllers\Workspace\LocalController;
use App\Http\Controllers\Workspace\UsuarioController;
use App\Http\Controllers\Erp\EmpresaDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Workspace\AppsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/

Auth::routes();

// Logout adicional
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Home temporal después de login
Route::get('/home', function () {
    return view('home');
})->middleware('auth')->name('home');

/*
|--------------------------------------------------------------------------
| Gestión de Contexto Multiempresa
|--------------------------------------------------------------------------
| Selector y cambio de contexto de empresa y local
*/

// use App\Http\Controllers\ContextoController;

// Route::middleware(['auth'])->prefix('contexto')->name('contexto.')->group(function () {
//     // Selector de contexto (empresa y local)
//     Route::get('/selector', [ContextoController::class, 'selector'])->name('selector');
    
//     // Cambiar contexto
//     Route::post('/cambiar', [ContextoController::class, 'cambiar'])->name('cambiar');
    
//     // Limpiar contexto
//     Route::post('/limpiar', [ContextoController::class, 'limpiar'])->name('limpiar');
    
//     // Cambio rápido de empresa (sin pasar por selector)
//     Route::post('/cambio-rapido', [ContextoController::class, 'cambioRapido'])->name('cambio-rapido');
    
//     // AJAX: Obtener locales de una empresa
//     Route::get('/locales', [ContextoController::class, 'getLocales'])->name('locales');
    
//     // AJAX: Obtener contexto actual
//     Route::get('/actual', [ContextoController::class, 'getContextoActual'])->name('actual');
// });

/*
|--------------------------------------------------------------------------
| Redireccionamiento Post-Login
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Dashboard genérico que redirige según el rol
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
});

/*
|--------------------------------------------------------------------------
| PANEL SUPERUSUARIO - /admin
|--------------------------------------------------------------------------
| Solo accesible por usuarios con rol 'superusuario'
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'superadmin'])
    ->group(function () {
        
        // Dashboard del superusuario
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // require __DIR__.'/admin.php';
        
        // // Gestión de grupos empresariales
        Route::resource('grupos', AdminGrupoController::class);
        // Route::post('grupos/{grupo}/activar', [AdminGrupoController::class, 'activar'])->name('grupos.activar');
        // Route::post('grupos/{grupo}/suspender', [AdminGrupoController::class, 'suspender'])->name('grupos.suspender');
        // Route::post('grupos/{grupo}/cambiar-plan', [AdminGrupoController::class, 'cambiarPlan'])->name('grupos.cambiar-plan');

        // Gestión de logs
        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
        Route::get('logs/dashboard', function () {return view('admin.logs.dashboard'); })->name('logs.dashboard');
        Route::get('logs/stats', [LogController::class, 'stats'])->name('logs.stats');
        Route::get('logs/{filename}', [LogController::class, 'show'])->name('logs.show');
        Route::get('logs/{filename}/download', [LogController::class, 'download'])->name('logs.download');
        Route::post('logs/clean', [LogController::class, 'clean'])->name('logs.clean');
        Route::delete('logs/{filename}', [LogController::class, 'delete'])->name('logs.delete');

        // Gestión de leads
        Route::resource('lead-cliente', LeadClienteController::class)->names([
            'index' => 'lead-cliente.index',
            'create' => 'lead-cliente.create',
            'store' => 'lead-cliente.store',
            'show' => 'lead-cliente.show',
            'edit' => 'lead-cliente.edit',
            'update' => 'lead-cliente.update',
            'destroy' => 'lead-cliente.destroy',
        ]);
        Route::post('lead-cliente/{leadCliente}/dar-de-alta', [LeadClienteController::class, 'darDeAlta'])->name('lead-cliente.dar-de-alta');


        
        // // Gestión global de usuarios
        // Route::get('usuarios', [AdminDashboardController::class, 'usuarios'])->name('usuarios.index');
        // Route::get('usuarios/{user}', [AdminDashboardController::class, 'usuarioDetalle'])->name('usuarios.show');
        
        // // Reportes y estadísticas globales
        // Route::get('reportes', [AdminDashboardController::class, 'reportes'])->name('reportes');
        // Route::get('actividad', [AdminDashboardController::class, 'actividad'])->name('actividad');
        
        // // Configuración del sistema
        // Route::get('configuracion', [AdminDashboardController::class, 'configuracion'])->name('configuracion');
        // Route::post('configuracion', [AdminDashboardController::class, 'guardarConfiguracion'])->name('configuracion.store');
        
        // // Logs del sistema
        // Route::get('logs', [AdminDashboardController::class, 'logs'])->name('logs');
        
    });

/*
|--------------------------------------------------------------------------
| PANEL GRUPO EMPRESARIAL - /{grupo}
|--------------------------------------------------------------------------
| Accesible por: superusuario, propietario, administrador_general
| Middleware: auth, grupo.access, rol.administrador
*/

Route::prefix('{grupo}')
    ->name('grupo.')
    ->middleware(['auth', 'grupo.access', 'rol.administrador'])
    ->group(function () {
        
        // Dashboard del grupo
        Route::get('/', [GrupoDashboardController::class, 'index'])->name('dashboard');

        // Apps del grupo
        Route::get('/apps', [AppsController::class, 'index'])->name('apps');
        
        // Gestión de empresas
        Route::resource('empresas', EmpresaController::class)->except(['index']);
        Route::get('empresas', [EmpresaController::class, 'index'])->name('empresas.index');
        Route::post('empresas/{empresa}/activar', [EmpresaController::class, 'activar'])->name('empresas.activar');
        Route::post('empresas/{empresa}/desactivar', [EmpresaController::class, 'desactivar'])->name('empresas.desactivar');
        
        // Gestión de sedes
        Route::resource('sedes', SedeController::class);
        Route::post('sedes/{sede}/activar', [SedeController::class, 'activar'])->name('sedes.activar');
        
        // Gestión de locales
        Route::resource('locales', LocalController::class);
        Route::post('locales/{local}/activar', [LocalController::class, 'activar'])->name('locales.activar');
        
        // // Gestión de usuarios del grupo
        // Route::resource('usuarios', UsuarioController::class);
        // Route::post('usuarios/{usuario}/activar', [UsuarioController::class, 'activar'])->name('usuarios.activar');
        // Route::post('usuarios/{usuario}/desactivar', [UsuarioController::class, 'desactivar'])->name('usuarios.desactivar');
        // Route::post('usuarios/{usuario}/asignar-empresa', [UsuarioController::class, 'asignarEmpresa'])->name('usuarios.asignar-empresa');
        
        // // Roles y permisos
        // Route::get('roles', [UsuarioController::class, 'roles'])->name('roles.index');
        // Route::post('roles', [UsuarioController::class, 'crearRol'])->name('roles.store');
        // Route::get('permisos', [UsuarioController::class, 'permisos'])->name('permisos.index');
        
        // Configuración del grupo
        Route::get('configuracion', [GrupoDashboardController::class, 'configuracion'])->name('configuracion');
        Route::post('configuracion', [GrupoDashboardController::class, 'guardarConfiguracion'])->name('configuracion.store');
        
        // Planes y módulos
        Route::get('plan', [GrupoDashboardController::class, 'plan'])->name('plan');
        Route::get('modulos', [GrupoDashboardController::class, 'modulos'])->name('modulos');
        
        // Reportes del grupo
        Route::get('reportes', [GrupoDashboardController::class, 'reportes'])->name('reportes');
        
});

/*
|--------------------------------------------------------------------------
| PANEL EMPRESA - /{grupo}/erp/{empresa}
|--------------------------------------------------------------------------
| Accesible por: todos los usuarios con acceso a la empresa
| Middleware: auth, grupo.access, empresa.access
*/

// Route::prefix('{grupo}/erp/{empresa}')
//     ->name('empresa.')
//     ->middleware(['auth', 'grupo.access', 'empresa.access'])
//     ->group(function () {
        
//         // Dashboard de la empresa
//         Route::get('/', [EmpresaDashboardController::class, 'index'])->name('dashboard');
        
//         // Módulos de la empresa (ERP, CRM, RRHH, etc.)
//         // Estos se cargarán dinámicamente según los módulos activos
        
//         // Módulo: Ventas
//         Route::prefix('ventas')->name('ventas.')->group(function () {
//             Route::get('/', function () {
//                 return view('empresa.ventas.index');
//             })->name('index');
//             Route::get('cotizaciones', function () {
//                 return view('empresa.ventas.cotizaciones');
//             })->name('cotizaciones');
//             Route::get('facturas', function () {
//                 return view('empresa.ventas.facturas');
//             })->name('facturas');
//         });
        
//         // Módulo: Inventario
//         Route::prefix('inventario')->name('inventario.')->group(function () {
//             Route::get('/', function () {
//                 return view('empresa.inventario.index');
//             })->name('index');
//             Route::get('productos', function () {
//                 return view('empresa.inventario.productos');
//             })->name('productos');
//             Route::get('stock', function () {
//                 return view('empresa.inventario.stock');
//             })->name('stock');
//         });
        
//         // Módulo: Compras
//         Route::prefix('compras')->name('compras.')->group(function () {
//             Route::get('/', function () {
//                 return view('empresa.compras.index');
//             })->name('index');
//             Route::get('ordenes', function () {
//                 return view('empresa.compras.ordenes');
//             })->name('ordenes');
//             Route::get('proveedores', function () {
//                 return view('empresa.compras.proveedores');
//             })->name('proveedores');
//         });
        
//         // Módulo: Clientes (CRM)
//         Route::prefix('clientes')->name('clientes.')->group(function () {
//             Route::get('/', function () {
//                 return view('empresa.clientes.index');
//             })->name('index');
//             Route::get('oportunidades', function () {
//                 return view('empresa.clientes.oportunidades');
//             })->name('oportunidades');
//         });
        
//         // Módulo: Contabilidad
//         Route::prefix('contabilidad')->name('contabilidad.')->group(function () {
//             Route::get('/', function () {
//                 return view('empresa.contabilidad.index');
//             })->name('index');
//             Route::get('plan-cuentas', function () {
//                 return view('empresa.contabilidad.plan-cuentas');
//             })->name('plan-cuentas');
//             Route::get('flujo-caja', function () {
//                 return view('empresa.contabilidad.flujo-caja');
//             })->name('flujo-caja');
//         });
        
//         // Módulo: Reportes de empresa
//         Route::prefix('reportes')->name('reportes.')->group(function () {
//             Route::get('/', function () {
//                 return view('empresa.reportes.index');
//             })->name('index');
//             Route::get('financieros', function () {
//                 return view('empresa.reportes.financieros');
//             })->name('financieros');
//             Route::get('ventas', function () {
//                 return view('empresa.reportes.ventas');
//             })->name('ventas');
//         });
        
//         // Configuración de la empresa (solo para administradores)
//         Route::middleware(['permission:configurar_empresa'])->group(function () {
//             Route::get('configuracion', [EmpresaDashboardController::class, 'configuracion'])->name('configuracion');
//             Route::post('configuracion', [EmpresaDashboardController::class, 'guardarConfiguracion'])->name('configuracion.store');
//         });
        
// });


/*
|--------------------------------------------------------------------------
| Rutas especiales
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth'])->group(function () {
    
//     // Cambiar contexto de empresa
//     Route::post('/cambiar-empresa/{empresa}', [DashboardController::class, 'cambiarEmpresa'])->name('cambiar-empresa');
    
//     // Perfil de usuario
//     Route::get('/perfil', [DashboardController::class, 'perfil'])->name('perfil');
//     Route::post('/perfil', [DashboardController::class, 'actualizarPerfil'])->name('perfil.update');
//     Route::post('/perfil/avatar', [DashboardController::class, 'actualizarAvatar'])->name('perfil.avatar');
    
//     // Notificaciones
//     Route::get('/notificaciones', [DashboardController::class, 'notificaciones'])->name('notificaciones');
//     Route::post('/notificaciones/{id}/leer', [DashboardController::class, 'marcarLeida'])->name('notificaciones.leer');
    
// });

/*
|--------------------------------------------------------------------------
| Plan expirado
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth', 'grupo.access'])->group(function () {
//     Route::get('/{grupo}/plan-expirado', function ($grupo) {
//         return view('errors.plan-expirado', compact('grupo'));
//     })->name('grupo.plan-expirado');
// });