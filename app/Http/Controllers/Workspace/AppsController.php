<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Models\GrupoEmpresa;
use App\Models\Scopes\FiltroMultiempresaScope;
use App\Models\Workspace\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $grupoActual = $request->get('grupoEmpresa');

        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }

        $empresa_id = $user->grupo_empresa_id;

        $empresas = $grupoActual->empresas()
            ->withCount('usuarios')
            ->latest()
            ->take(10)
            ->get();

        
        // Definir las aplicaciones disponibles
        $aplicaciones = [
            [
                'id' => 'erp',
                'nombre' => 'ERP',
                'descripcion' => 'Sistema de Planificación de Recursos Empresariales',
                'icono' => 'ti ti-building-store',
                'color' => 'primary',
                'activa' => true,
                'ruta_base' => ''
            ],
            [
                'id' => 'crm',
                'nombre' => 'CRM',
                'descripcion' => 'Gestión de Relaciones con Clientes',
                'icono' => 'ti ti-users',
                'color' => 'success',
                'activa' => false,
                'ruta_base' => '/crm'
            ],
            [
                'id' => 'rrhh',
                'nombre' => 'RRHH',
                'descripcion' => 'Recursos Humanos',
                'icono' => 'ti ti-user-check',
                'color' => 'warning',
                'activa' => false,
                'ruta_base' => '/rrhh'
            ],
            [
                'id' => 'web',
                'nombre' => 'Web',
                'descripcion' => 'Sitio Web Corporativo',
                'icono' => 'ti ti-world',
                'color' => 'info',
                'activa' => false,
                'ruta_base' => '/web'
            ],
            [
                'id' => 'ecommerce',
                'nombre' => 'E-Commerce',
                'descripcion' => 'Tienda en Línea',
                'icono' => 'ti ti-shopping-cart',
                'color' => 'danger',
                'activa' => false,
                'ruta_base' => '/ecommerce'
            ],
            // [
            //     'id' => 'restaurante',
            //     'nombre' => 'Restaurante',
            //     'descripcion' => 'Gestión de Restaurante',
            //     'icono' => 'ti ti-chef-hat',
            //     'color' => 'danger',
            //     'activa' => false,
            //     'ruta_base' => '/rs'
            // ]
        ];

        return view('apps.workspace.apps.index', compact('grupoActual', 'aplicaciones', 'user', 'empresas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $grupoempresa)
    {
        $user = Auth::user();
        
        // Buscar el grupo empresarial por slug o id
        $grupo = GrupoEmpresarial::where('slug', $grupoempresa)
            ->orWhere('id', $grupoempresa)
            ->where('user_uuid', $user->id)
            ->firstOrFail();
            
        return view('workspace.grupo', compact('grupo', 'user'));
    }

    /**
     * Configuración del grupo empresarial
     */
    public function configuracion(string $grupoempresa)
    {
        $user = Auth::user();
        
        $grupo = GrupoEmpresarial::where('slug', $grupoempresa)
            ->orWhere('id', $grupoempresa)
            ->where('user_uuid', $user->id)
            ->firstOrFail();
            
        return view('workspace.configuracion.index', compact('grupo', 'user'));
    }

    /**
     * Gestión de empresas del grupo
     */
    public function empresas(string $grupoempresa)
    {
        $user = Auth::user();
        
        $grupo = GrupoEmpresarial::where('slug', $grupoempresa)
            ->orWhere('id', $grupoempresa)
            ->where('user_uuid', $user->id)
            ->with('empresas')
            ->firstOrFail();
            
        return view('workspace.configuracion.empresas', compact('grupo', 'user'));
    }

    /**
     * Gestión de usuarios del grupo
     */
    public function usuarios(string $grupoempresa)
    {
        $user = Auth::user();
        
        $grupo = GrupoEmpresarial::where('slug', $grupoempresa)
            ->orWhere('id', $grupoempresa)
            ->where('user_uuid', $user->id)
            ->firstOrFail();
            
        return view('workspace.configuracion.usuarios', compact('grupo', 'user'));
    }

    /**
     * Reportes del grupo
     */
    public function reportes(string $grupoempresa)
    {
        $user = Auth::user();
        
        $grupo = GrupoEmpresarial::where('slug', $grupoempresa)
            ->orWhere('id', $grupoempresa)
            ->where('user_uuid', $user->id)
            ->firstOrFail();
            
        return view('workspace.reportes.index', compact('grupo', 'user'));
    }

    /**
     * Reporte general del grupo
     */
    public function reporteGeneral(string $grupoempresa)
    {
        $user = Auth::user();
        
        $grupo = GrupoEmpresarial::where('slug', $grupoempresa)
            ->orWhere('id', $grupoempresa)
            ->where('user_uuid', $user->id)
            ->with('empresas')
            ->firstOrFail();
            
        return view('workspace.reportes.general', compact('grupo', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
