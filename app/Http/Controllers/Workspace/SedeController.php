<?php

namespace App\Http\Controllers\Workspace;

use App\Http\Controllers\Controller;
use App\Http\Requests\Workspace\SedeRequest;
use App\Models\Workspace\Empresa;
use App\Models\Workspace\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SedeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        
        $this->middleware('can:ver_sedes')->only('index', 'show');
        $this->middleware('can:crear_sedes')->only('create', 'store');
        $this->middleware('can:editar_sedes')->only('edit', 'update');
        $this->middleware('can:eliminar_sedes')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener el grupo actual del contexto (inyectado por middleware grupo.access)
        $grupoActual = $request->input('grupoEmpresa');
        $user = $request->user(); // Obtener el usuario autenticado

        if(!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }

        // Las sedes pertenecen directamente al grupo empresarial, no a empresas individuales
        // Filtrar sedes del grupo actual
        $sedes = Sede::with('grupoEmpresa')
            ->where('grupo_empresa_id', $grupoActual->id)
            ->get();

        Log::info('Listando sedes para grupo: ' . $grupoActual->nombre_comercial . ' - Total: ' . $sedes->count());

        return view('apps.workspace.sedes.index', compact('sedes', 'grupoActual'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, string $grupo)
    {
        // Obtener el grupo actual del contexto
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        Log::info('Mostrando formulario para crear sede en grupo: ' . $grupoActual->nombre_comercial);
        
        return view('apps.workspace.sedes.create', compact('grupoActual'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SedeRequest $request, string $grupo)
    {
        // Obtener el grupo actual del contexto
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        Log::info('Creando nueva sede para grupo: ' . $grupoActual->nombre_comercial);

        try {
            DB::beginTransaction();

            $data = array_merge(
                $request->validated(),
                ['grupo_empresa_id' => $grupoActual->id]
            );

            $sede = Sede::create($data);

            DB::commit();

            Log::info('Sede creada exitosamente: ' . $sede->id);

            return redirect()->route('grupo.sedes.index', ['grupo' => $grupoActual->slug])
                ->with('success', 'Sede creada exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Error de base de datos al crear sede: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return back()
                    ->withInput()
                    ->with('error', 'Ya existe una sede con este nombre para la empresa seleccionada.');
            }
            
            return back()
                ->withInput()
                ->with('error', 'Error de base de datos al crear la sede. Verifique los datos e inténtelo nuevamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error general al crear sede: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Error inesperado al crear la sede. Inténtalo nuevamente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $grupo, Sede $sede)
    {
        // Obtener el grupo actual del contexto
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        // Verificar que la sede pertenece al grupo actual
        if ($sede->grupo_empresa_id !== $grupoActual->id) {
            abort(403, 'No tienes permisos para ver esta sede.');
        }
        
        // Cargar la relación con grupo empresarial
        $sede->load('grupoEmpresa');
        
        // Obtener otras sedes del mismo grupo (excluyendo la actual)
        $otrasSedesGrupo = Sede::where('grupo_empresa_id', $sede->grupo_empresa_id)
            ->where('id', '!=', $sede->id)
            ->orderBy('nombre')
            ->get();

        return view('apps.workspace.sedes.show', compact('sede', 'otrasSedesGrupo', 'grupoActual'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $grupo, Sede $sede)
    {
        // Obtener el grupo actual del contexto
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        // Verificar que la sede pertenece al grupo actual
        if ($sede->grupo_empresa_id !== $grupoActual->id) {
            abort(403, 'No tienes permisos para editar esta sede.');
        }
        
        // Obtener otras sedes del mismo grupo (excluyendo la actual)
        $otrasSedesGrupo = Sede::where('grupo_empresa_id', $sede->grupo_empresa_id)
            ->where('id', '!=', $sede->id)
            ->orderBy('nombre')
            ->get();
            
        return view('apps.workspace.sedes.edit', compact('sede', 'otrasSedesGrupo', 'grupoActual'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SedeRequest $request, string $grupo, Sede $sede)
    {
        // Obtener el grupo actual del contexto
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        // Verificar que la sede pertenece al grupo actual
        if ($sede->grupo_empresa_id !== $grupoActual->id) {
            abort(403, 'No tienes permisos para actualizar esta sede.');
        }
        
        try {
            DB::beginTransaction();

            $sede->update($request->validated());

            DB::commit();

            return redirect()
                ->route('grupo.sedes.index', ['grupo' => $grupoActual->slug])
                ->with('success', 'Sede actualizada exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Error de base de datos al actualizar sede: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return back()
                    ->withInput()
                    ->with('error', 'Ya existe una sede con este nombre para la empresa seleccionada.');
            }
            
            return back()
                ->withInput()
                ->with('error', 'Error de base de datos al actualizar la sede. Verifique los datos e inténtelo nuevamente.');
                
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error general al actualizar sede: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Error inesperado al actualizar la sede. Inténtalo nuevamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $grupo, Sede $sede)
    {
        // Obtener el grupo actual del contexto
        $grupoActual = $request->input('grupoEmpresa');
        
        if (!$grupoActual) {
            abort(404, 'Grupo empresarial no encontrado');
        }
        
        // Verificar que la sede pertenece al grupo actual
        if ($sede->grupo_empresa_id !== $grupoActual->id) {
            abort(403, 'No tienes permisos para eliminar esta sede.');
        }
        
        try {
            DB::beginTransaction();

            $nombreSede = $sede->nombre;
            $sede->delete();

            DB::commit();

            return redirect()
                ->route('grupo.sedes.index', ['grupo' => $grupoActual->slug])
                ->with('success', 'Sede eliminada exitosamente.');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            Log::error('Error de base de datos al eliminar sede: ' . $e->getMessage());
            
            if ($e->getCode() == 23000) {
                return back()->with('error', 'No se puede eliminar la sede porque tiene registros relacionados en el sistema.');
            }
            
            return back()->with('error', 'Error de base de datos al eliminar la sede. Inténtalo nuevamente.');
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error general al eliminar sede: ' . $e->getMessage());

            return back()->with('error', 'Error inesperado al eliminar la sede. Inténtalo nuevamente.');
        }
    }
}
