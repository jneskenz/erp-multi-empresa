<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrupoEmpresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard principal del superusuario
     */
    public function index()
    {
        // Estadísticas generales
        // $stats = [
        //     'total_grupos' => GrupoEmpresa::count(),
        //     'grupos_activos' => GrupoEmpresa::where('activo', true)->count(),
        //     'total_usuarios' => User::count(),
        //     'usuarios_activos' => User::where('activo', true)->count(),
        // ];
        
        // // Grupos empresariales recientes
        // $gruposRecientes = GrupoEmpresa::with('propietarios')
        //     ->latest()
        //     ->take(10)
        //     ->get();
        
        // // Actividad reciente del sistema
        // $actividadReciente = Activity::with('causer', 'subject')
        //     ->latest()
        //     ->take(20)
        //     ->get();
        
        // // Usuarios registrados recientemente
        // $usuariosRecientes = User::latest()
        //     ->take(10)
        //     ->get();
        

        // return view('apps.admin.dashboard', compact(
        //     'stats',
        //     'gruposRecientes',
        //     'actividadReciente',
        //     'usuariosRecientes'
        // ));

        return view('admin.dashboard');
    }
    
    /**
     * Lista de usuarios del sistema
     */
    public function usuarios(Request $request)
    {
        $query = User::with('grupoEmpresa', 'empresa', 'roles');
        
        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('grupo_id')) {
            $query->where('grupo_empresa_id', $request->grupo_id);
        }
        
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }
        
        $usuarios = $query->paginate(20);
        $grupos = GrupoEmpresa::orderBy('nombre')->get();
        
        return view('apps.admin.usuarios.index', compact('usuarios', 'grupos'));
    }
    
    /**
     * Detalle de un usuario
     */
    public function usuarioDetalle(User $user)
    {
        $user->load('grupoEmpresa', 'empresa', 'roles', 'permissions');
        
        // Actividad del usuario
        $actividad = Activity::where('causer_id', $user->id)
            ->latest()
            ->take(50)
            ->get();
        
        return view('apps.admin.usuarios.show', compact('user', 'actividad'));
    }
    
    /**
     * Reportes globales
     */
    public function reportes()
    {
        // Reportes por plan
        $reportePorPlan = GrupoEmpresa::select('plan_actual', DB::raw('count(*) as total'))
            ->groupBy('plan_actual')
            ->get();
        
        // Crecimiento mensual de usuarios
        $usuariosPorMes = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('count(*) as total')
            )
            ->groupBy('mes')
            ->orderBy('mes', 'desc')
            ->take(12)
            ->get();
        
        return view('apps.admin.reportes.index', compact(
            'reportePorPlan',
            'usuariosPorMes'
        ));
    }
    
    /**
     * Logs de actividad del sistema
     */
    public function actividad(Request $request)
    {
        $query = Activity::with('causer', 'subject');
        
        // Filtros
        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }
        
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id);
        }
        
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }
        
        $actividades = $query->latest()->paginate(50);
        
        return view('apps.admin.actividad.index', compact('actividades'));
    }
    
    /**
     * Configuración del sistema
     */
    public function configuracion()
    {
        // Aquí se cargarían las configuraciones del sistema
        $config = config('superadmin');
        
        return view('apps.admin.configuracion.index', compact('config'));
    }
    
    /**
     * Guardar configuración del sistema
     */
    public function guardarConfiguracion(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'maintenance_mode' => 'boolean',
            'allow_registration' => 'boolean',
            'default_plan' => 'required|string',
        ]);
        
        // Aquí guardarías las configuraciones en .env o database
        
        activity()
            ->causedBy(Auth::user())
            ->withProperties($validated)
            ->log('Actualizó configuración del sistema');
        
        return redirect()->back()->with('success', 'Configuración guardada correctamente.');
    }
    
    /**
     * Logs del sistema
     */
    public function logs(Request $request)
    {
        // Leer logs de Laravel
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        
        if (file_exists($logFile)) {
            $lines = file($logFile);
            $logs = array_slice(array_reverse($lines), 0, 100);
        }
        
        return view('apps.admin.logs.index', compact('logs'));
    }
}
