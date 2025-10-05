<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Models\Workspace\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class EmpresaDashboardController extends Controller
{
    /**
     * Dashboard principal de la empresa
     */
    public function index(Request $request)
    {
        $grupo = $request->get('grupoEmpresa');
        $empresa = $request->get('empresa');
        
        if (!$empresa) {
            abort(404, 'Empresa no encontrada');
        }
        
        // Verificar módulos activos
        $modulosActivos = $empresa->modulos_activos ?? [];
        
        // Estadísticas generales de la empresa
        $stats = [
            'total_usuarios' => $empresa->usuarios()->count(),
            'usuarios_activos' => $empresa->usuarios()->where('activo', true)->count(),
            'total_locales' => $empresa->locales()->count() ?? 0,
            'locales_activos' => $empresa->locales()->where('activo', true)->count() ?? 0,
        ];
        
        // Si tiene módulo ERP, cargar estadísticas adicionales
        if (in_array('ERP', $modulosActivos)) {
            // Aquí irían las estadísticas de ventas, compras, inventario, etc.
            // Por ahora dejamos placeholders
            $stats['ventas_mes'] = 0;
            $stats['compras_mes'] = 0;
            $stats['productos_stock_bajo'] = 0;
        }
        
        // Si tiene módulo CRM
        if (in_array('CRM', $modulosActivos)) {
            $stats['clientes_activos'] = 0;
            $stats['oportunidades_abiertas'] = 0;
        }
        
        // Actividad reciente en la empresa
        // $actividadReciente = activity()
        //     ->inLog('default')
        //     ->where(function($query) use ($empresa) {
        //         $query->where('subject_type', get_class($empresa))
        //               ->where('subject_id', $empresa->id);
        //     })
        //     ->with('causer')
        //     ->latest()
        //     ->take(15)
        //     ->get();

        // activity() devuelve una instancia de Spatie\Activitylog\ActivityLogger, no una consulta Eloquent.
        // Por eso no puedes usar where() ni with() ni latest() sobre activity().
        $actividadReciente = Activity::inLog('default')
        ->where(function($query) use ($grupo) {
            $query->where('subject_type', get_class($grupo))
                ->where('subject_id', $grupo->id);
        })
        ->orWhere(function($query) use ($grupo) {
            $query->whereHasMorph('subject', [Empresa::class], function($q) use ($grupo) {
                $q->where('grupo_empresa_id', $grupo->id);
            });
        })
        ->with('causer')
        ->latest()
        ->take(20)
        ->get();
        
        // Usuarios activos en la empresa
        $usuariosEmpresa = $empresa->usuarios()
            ->with('roles')
            ->where('activo', true)
            ->latest('ultimo_acceso')
            ->take(10)
            ->get();
        
        return view('apps.erp.empresa.dashboard', compact(
            'grupo',
            'empresa',
            'stats',
            'modulosActivos',
            'actividadReciente',
            'usuariosEmpresa'
        ));
    }
    
    /**
     * Configuración de la empresa (solo para administradores)
     */
    public function configuracion(Request $request)
    {
        $grupo = $request->get('grupoEmpresa');
        $empresa = $request->get('empresa');
        
        return view('apps.erp.empresa.configuracion', compact('grupo', 'empresa'));
    }
    
    /**
     * Guardar configuración de la empresa
     */
    public function guardarConfiguracion(Request $request)
    {
        $empresa = $request->get('empresa');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'email' => 'required|email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'configuracion_visual' => 'nullable|array',
        ]);
        
        $empresa->update($validated);
        
        activity()
            ->performedOn($empresa)
            ->causedBy(Auth::user())
            ->log('Actualizó configuración de la empresa');
        
        return redirect()
            ->back()
            ->with('success', 'Configuración guardada correctamente.');
    }
}
