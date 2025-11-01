<?php

use App\Http\Controllers\Admin\LogController;
use Illuminate\Support\Facades\Route;


// Rutas de personalización
// Route::prefix('customization')->name('customization.')->group(function () {
//    Route::get('/', [CustomizationController::class, 'index'])->name('index');
//    Route::post('/update', [CustomizationController::class, 'update'])->name('update');
//    Route::post('/reset', [CustomizationController::class, 'reset'])->name('reset');
//    Route::get('/settings', [CustomizationController::class, 'getSettings'])->name('settings');
// });

Route::get('logs', [LogController::class, 'index'])->name('logs.index');
Route::get('logs/dashboard', function () {
   return view('admin.logs.dashboard');
})->name('logs.dashboard');
Route::get('logs/stats', [LogController::class, 'stats'])->name('logs.stats');
Route::get('logs/{filename}', [LogController::class, 'show'])->name('logs.show');
Route::get('logs/{filename}/download', [LogController::class, 'download'])->name('logs.download');
Route::delete('logs/{filename}', [LogController::class, 'delete'])->name('logs.delete');
Route::post('logs/clean', [LogController::class, 'clean'])->name('logs.clean');

// Rutas de Grupos Empresariales
// Route::resource('grupo-empresarial', GrupoEmpresaController::class)->names([
//    'index' => 'grupo-empresarial.index',
//    'create' => 'grupo-empresarial.create',
//    'store' => 'grupo-empresarial.store',
//    'show' => 'grupo-empresarial.show',
//    'edit' => 'grupo-empresarial.edit',
//    'update' => 'grupo-empresarial.update',
//    'destroy' => 'grupo-empresarial.destroy',
// ]);
// Route::post('grupo-empresarial/{grupoEmpresarial}/toggle-status', [GrupoEmpresaController::class, 'toggleStatus'])->name('grupo-empresarial.toggle-status');

