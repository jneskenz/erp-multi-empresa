<?php

use App\Http\Controllers\Erp\EmpresaController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Ruta de prueba
    Route::get('/test', function () {
        return view('test');
    })->name('test');
    
    // Rutas de usuarios
    Route::resource('users', UserController::class);
    
    // Rutas de roles
    Route::resource('roles', RoleController::class);

    Route::resource('empresas', EmpresaController::class);

    // Rutas de sedes
    Route::resource('sedes', App\Http\Controllers\Erp\SedeController::class);

    // Rutas de locales
    Route::resource('locales', App\Http\Controllers\Erp\LocalController::class);
    Route::post('locales/{locale}/toggle-status', [App\Http\Controllers\Erp\LocalController::class, 'toggleStatus'])->name('locales.toggle-status');

    // Rutas de personalización
    Route::prefix('customization')->name('customization.')->group(function () {
        Route::get('/', [App\Http\Controllers\CustomizationController::class, 'index'])->name('index');
        Route::post('/update', [App\Http\Controllers\CustomizationController::class, 'update'])->name('update');
        Route::post('/reset', [App\Http\Controllers\CustomizationController::class, 'reset'])->name('reset');
        Route::get('/settings', [App\Http\Controllers\CustomizationController::class, 'getSettings'])->name('settings');
    });

    // Rutas de administración de logs (solo para superadmin)
    Route::prefix('admin')->name('admin.')->middleware('superadmin')->group(function () {
        Route::get('logs', [App\Http\Controllers\Admin\LogController::class, 'index'])->name('logs.index');
        Route::get('logs/dashboard', function () {
            return view('admin.logs.dashboard');
        })->name('logs.dashboard');
        Route::get('logs/stats', [App\Http\Controllers\Admin\LogController::class, 'stats'])->name('logs.stats');
        Route::get('logs/{filename}', [App\Http\Controllers\Admin\LogController::class, 'show'])->name('logs.show');
        Route::get('logs/{filename}/download', [App\Http\Controllers\Admin\LogController::class, 'download'])->name('logs.download');
        Route::delete('logs/{filename}', [App\Http\Controllers\Admin\LogController::class, 'delete'])->name('logs.delete');
        Route::post('logs/clean', [App\Http\Controllers\Admin\LogController::class, 'clean'])->name('logs.clean');
    });


});
