<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\GrupoEmpresa;
use App\Models\Workspace\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class GrupoDashboardController extends Controller
{
    /**
     * Dashboard del grupo empresarial
     */
    public function index(Request $request)
    {
        $grupo = $request->get('grupoEmpresa');
        
        if (!$grupo) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        // Estadísticas del grupo
        $stats = [
            'total_empresas' => $grupo->empresas()->count(),
            'empresas_activas' => $grupo->empresas()->where('activo', true)->count(),
            'total_usuarios' => $grupo->usuarios()->count(),
            'usuarios_activos' => $grupo->usuarios()->where('activo', true)->count(),
            'total_sedes' => $grupo->sedes()->count() ?? 0,
            'total_locales' => $grupo->locales()->count() ?? 0,
        ];
        
        // Empresas del grupo
        $empresas = $grupo->empresas()
            ->withCount('usuarios')
            ->latest()
            ->take(10)
            ->get();
        
        // Usuarios recientes
        $usuariosRecientes = $grupo->usuarios()
            ->with('empresa', 'roles')
            ->latest()
            ->take(10)
            ->get();
        
        // Actividad reciente del grupo
        // $actividadReciente = activity()
        //     ->inLog('default')
        //     ->where(function($query) use ($grupo) {
        //         $query->where('subject_type', get_class($grupo))
        //               ->where('subject_id', $grupo->id);
        //     })
        //     ->orWhere(function($query) use ($grupo) {
        //         $query->whereHasMorph('subject', [Empresa::class], function($q) use ($grupo) {
        //             $q->where('grupo_empresa_id', $grupo->id);
        //         });
        //     })
        //     ->with('causer')
        //     ->latest()
        //     ->take(20)
        //     ->get();

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
            
        return view('apps.workspace.grupo.dashboard', compact(
            'grupo',
            'stats',
            'empresas',
            'usuariosRecientes',
            'actividadReciente'
        ));
    }
    
    /**
     * Configuración del grupo
     */
    public function configuracion(Request $request)
    {
        $grupo = $request->get('grupoEmpresa');
        
        return view('apps.workspace.grupo.configuracion', compact('grupo'));
    }
    
    /**
     * Guardar configuración del grupo
     */
    public function guardarConfiguracion(Request $request)
    {
        $grupo = $request->get('grupoEmpresa');
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'ruc' => 'nullable|string|max:20',
            'email' => 'required|email',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'sitio_web' => 'nullable|url',
            'configuracion_visual' => 'nullable|array',
        ]);
        
        $grupo->update($validated);
        
        activity()
            ->performedOn($grupo)
            ->causedBy(Auth::user())
            ->log('Actualizó configuración del grupo');
        
        return redirect()
            ->back()
            ->with('success', 'Configuración guardada correctamente.');
    }
    
    /**
     * Ver plan actual y módulos
     */
    public function plan(Request $request)
    {
        $grupo = $request->get('grupoEmpresa');
        
        // Información del plan
        $planInfo = [
            'nombre' => $grupo->plan_actual,
            'max_empresas' => $grupo->max_empresas,
            'max_usuarios' => $grupo->max_usuarios,
            'fecha_inicio' => $grupo->fecha_inicio_plan,
            'fecha_fin' => $grupo->fecha_fin_plan,
            'dias_restantes' => $grupo->diasRestantesPlan(),
        ];
        
        // Uso actual
        $usoActual = [
            'empresas_usadas' => $grupo->empresas()->count(),
            'usuarios_usados' => $grupo->usuarios()->count(),
        ];
        
        return view('apps.workspace.grupo.plan', compact('grupo', 'planInfo', 'usoActual'));
    }
    
    /**
     * Ver módulos disponibles
     */
    public function modulos(Request $request)
    {
        $grupo = $request->get('grupoEmpresa');
        
        $modulosDisponibles = $grupo->modulos_disponibles ?? [];
        
        return view('apps.workspace.grupo.modulos', compact('grupo', 'modulosDisponibles'));
    }
    
    /**
     * Reportes del grupo
     */
    public function reportes(Request $request)
    {
        $grupo = $request->get('grupoEmpresa');
        
        // Reporte por empresa
        $reportePorEmpresa = $grupo->empresas()
            ->withCount('usuarios')
            ->get();
        
        // Usuarios por rol
        $usuariosPorRol = User::where('grupo_empresa_id', $grupo->id)
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as total'))
            ->groupBy('roles.name')
            ->get();
        
        return view('apps.workspace.grupo.reportes', compact(
            'grupo',
            'reportePorEmpresa',
            'usuariosPorRol'
        ));
    }
}
