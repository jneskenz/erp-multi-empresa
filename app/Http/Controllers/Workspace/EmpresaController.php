<?php

namespace App\Http\Controllers\Workspace;

use Illuminate\Http\Request;
use App\Models\Workspace\Empresa;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Workspace\EmpresaRequest;
use Illuminate\Support\Str;

class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Verificar permisos según el sistema de roles multiempresa
        
        $this->middleware('can:ver_empresas')->only('index', 'show');
        $this->middleware('can:crear_empresas')->only('create', 'store');
        $this->middleware('can:editar_empresas')->only('edit', 'update');
        $this->middleware('can:eliminar_empresas')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Obtener el grupo actual del contexto (inyectado por middleware grupo.access)
        $grupoActual = $request->input('grupoEmpresa');
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }

        // Iniciar query base con el grupo actual
        $query = Empresa::where('grupo_empresa_id', $grupoActual->id);

        // ==================== FILTRAR POR ROL ====================
        
        // Superusuario: ve todas las empresas del grupo (sin filtro adicional)
        // Propietario o Administrador General: ven todas las empresas del grupo (sin filtro adicional)
        if (!$user->esSuperusuario() && !$user->puedeGestionarGrupo($grupoActual->id)) {
            // Usuarios operativos: solo ven empresas asignadas del grupo
            $empresasIds = $user->getEmpresasConAccesoDeGrupo($grupoActual->id)->pluck('id')->toArray();
            
            if (empty($empresasIds)) {
                // Usuario sin empresas asignadas en este grupo
                $query->whereRaw('1 = 0'); // Query que no retorna resultados
            } else {
                $query->whereIn('id', $empresasIds);
            }
        }

        // ==================== BÚSQUEDA Y FILTROS ====================

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('razon_social', 'like', '%'.$request->input('buscar').'%')
                    ->orWhere('nombre_comercial', 'like', '%'.$request->input('buscar').'%')
                    ->orWhere('ruc', 'like', '%'.$request->input('buscar').'%');
            });
        }
        
        if ($request->filled('activo')) {
            $query->where('activo', $request->input('activo'));
        }

        // Obtener empresas paginadas
        $empresas = $query->orderBy('nombre_comercial')->paginate(10)->withQueryString();

        // ==================== ESTADÍSTICAS ====================
        
        // Estadísticas filtradas por el mismo criterio de acceso
        $queryEstadisticas = Empresa::where('grupo_empresa_id', $grupoActual->id);
        
        // Aplicar mismo filtro de rol para estadísticas
        if (!$user->esSuperusuario() && !$user->puedeGestionarGrupo($grupoActual->id)) {
            $empresasIds = $user->getEmpresasConAccesoDeGrupo($grupoActual->id)->pluck('id')->toArray();
            if (empty($empresasIds)) {
                $queryEstadisticas->whereRaw('1 = 0');
            } else {
                $queryEstadisticas->whereIn('id', $empresasIds);
            }
        }
        
        $totalEmpresas = $queryEstadisticas->count();
        $empresasActivas = (clone $queryEstadisticas)->where('activo', true)->count();

        return view('apps.workspace.empresas.index', compact('empresas', 'totalEmpresas', 'empresasActivas', 'grupoActual'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Obtener el grupo actual del contexto (inyectado por middleware grupo.access)
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        return view('apps.workspace.empresas.create', ['grupoActual' => $grupoActual]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmpresaRequest $request, string $grupo, Empresa $empresa)
    {
        // Obtener el grupo actual del contexto (inyectado por middleware grupo.access)
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }

        Log::info('Creando empresa en grupo: ' . $grupoActual->nombre_comercial);

        try {
            DB::beginTransaction();

            $data = array_merge(
                $request->validated(),
                ['grupo_empresa_id' => $grupoActual->id]
            );

            // ==================== MANEJAR SUBIDA DE LOGO ====================
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('empresas/logos', 'public');
                $data['logo'] = $logoPath;
                Log::info('Logo subido: ' . $logoPath);
            }

            // ==================== MANEJAR SUBIDA DE FAVICON ====================
            if ($request->hasFile('favicon')) {
                $faviconPath = $request->file('favicon')->store('empresas/favicons', 'public');
                $data['favicon'] = $faviconPath;
                Log::info('Favicon subido: ' . $faviconPath);
            }

            // Crear empresa
            $data['slug'] = Str::slug($data['nombre_comercial']);
            $empresa = Empresa::create($data);

            Log::info('Empresa creada con ID: ' . $empresa->id, (array)$data);

            // El activity log se registra automáticamente por el trait LogsActivity en el modelo

            DB::commit();

            Log::info('Empresa creada exitosamente: ' . $empresa->id);
            
            return redirect()->route('grupo.empresas.index', ['grupo' => $grupoActual->slug])
                ->with('success', 'Empresa creada exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Error de base de datos al crear empresa: ' . $e->getMessage());
            
            // Verificar si es error de duplicado (código de error 23000)
            if ($e->getCode() == 23000) {
                return back()
                    ->withInput()
                    ->with('error', 'El número de documento (RUC) ya está registrado en el sistema.');
            }
            
            return back()
                ->withInput()
                ->with('error', 'Error de base de datos al crear la empresa. Verifique los datos e inténtelo nuevamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error general al crear empresa: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Error inesperado al crear la empresa. Inténtalo nuevamente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $grupo, Empresa $empresa)
    {
        // Obtener el grupo actual del contexto (inyectado por middleware grupo.access)
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        // Verificar que la empresa pertenece al grupo actual
        if ($empresa->grupo_empresa_id !== $grupoActual->id) {
            abort(403, 'No tienes permisos para ver esta empresa.');
        }
        
        // Obtener las actividades relacionadas a esta empresa
        $activities = $empresa->activities()->latest()->get();

        return view('apps.workspace.empresas.show', compact('empresa', 'activities', 'grupoActual'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $grupo, Empresa $empresa)
    {
        // Obtener el grupo actual del contexto (inyectado por middleware grupo.access)
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        // Verificar que la empresa pertenece al grupo actual
        if ($empresa->grupo_empresa_id !== $grupoActual->id) {
            abort(403, 'Esta empresa no pertenece al grupo actual');
        }
        
        return view('apps.workspace.empresas.edit', compact('empresa', 'grupoActual'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmpresaRequest $request, string $grupo, Empresa $empresa)
    {
        // Obtener el grupo actual del contexto
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }

        try {
            DB::beginTransaction();

            $data = $request->validated();

            // ==================== MANEJAR LOGO ====================
            
            // Eliminar logo existente si se solicitó
            if ($request->has('remove_logo') && $request->remove_logo) {
                if ($empresa->logo) {
                    Storage::disk('public')->delete($empresa->logo);
                    $data['logo'] = null;
                    Log::info('Logo eliminado de empresa ID: ' . $empresa->id);
                }
            }
            
            // Subir nuevo logo
            if ($request->hasFile('logo')) {
                // Eliminar logo anterior si existe
                if ($empresa->logo) {
                    Storage::disk('public')->delete($empresa->logo);
                    Log::info('Logo anterior eliminado de empresa ID: ' . $empresa->id);
                }
                
                $logoPath = $request->file('logo')->store('empresas/logos', 'public');
                $data['logo'] = $logoPath;
                Log::info('Nuevo logo subido: ' . $logoPath);
            }

            // ==================== MANEJAR FAVICON ====================
            
            // Eliminar favicon existente si se solicitó
            if ($request->has('remove_favicon') && $request->remove_favicon) {
                if ($empresa->favicon) {
                    Storage::disk('public')->delete($empresa->favicon);
                    $data['favicon'] = null;
                    Log::info('Favicon eliminado de empresa ID: ' . $empresa->id);
                }
            }
            
            // Subir nuevo favicon
            if ($request->hasFile('favicon')) {
                // Eliminar favicon anterior si existe
                if ($empresa->favicon) {
                    Storage::disk('public')->delete($empresa->favicon);
                    Log::info('Favicon anterior eliminado de empresa ID: ' . $empresa->id);
                }
                
                $faviconPath = $request->file('favicon')->store('empresas/favicons', 'public');
                $data['favicon'] = $faviconPath;
                Log::info('Nuevo favicon subido: ' . $faviconPath);
            }

            $empresa->update($data);

            // El activity log se registra automáticamente por el trait LogsActivity en el modelo

            DB::commit();
            
            return redirect()
                ->route('grupo.empresas.index', ['grupo' => $grupoActual->slug])
                ->with('success', 'Empresa actualizada exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Error de base de datos al actualizar empresa: ' . $e->getMessage());
            
            // Verificar si es error de duplicado (código de error 23000)
            if ($e->getCode() == 23000) {
                return back()
                    ->withInput()
                    ->with('error', 'El número de documento (RUC) ya está registrado en el sistema.');
            }
            
            return back()
                ->withInput()
                ->with('error', 'Error de base de datos al actualizar la empresa. Verifique los datos e inténtelo nuevamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error general al actualizar empresa: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Error inesperado al actualizar la empresa. Inténtalo nuevamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        try {
            DB::beginTransaction();

            $nombreEmpresa = $empresa->nombre_comercial ?? $empresa->razon_social;
            $empresaData = $empresa->toArray(); // Guardar datos antes de eliminar
            $empresa->delete();

            // El activity log se registra automáticamente por el trait LogsActivity en el modelo

            DB::commit();

            // Obtener el grupo actual desde la ruta
            $grupoSlug = request()->route('grupo');
            
            return redirect()
                ->route('grupo.empresas.index', ['grupo' => $grupoSlug])
                ->with('success', 'Empresa eliminada exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Error de base de datos al eliminar empresa: ' . $e->getMessage());
            
            // Verificar si es error de integridad referencial (código de error 23000)
            if ($e->getCode() == 23000) {
                return back()->with('error', 'No se puede eliminar la empresa porque tiene registros relacionados en el sistema.');
            }
            
            return back()->with('error', 'Error de base de datos al eliminar la empresa. Inténtalo nuevamente.');
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error general al eliminar empresa: ' . $e->getMessage());

            return back()->with('error', 'Error inesperado al eliminar la empresa. Inténtalo nuevamente.');
        }
    }
}
