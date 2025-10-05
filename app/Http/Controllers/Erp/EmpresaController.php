<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Erp\EmpresaRequest;
use App\Models\Erp\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Aquí puedes agregar middleware para permisos específicos si es necesario

        $this->middleware('can:empresas.view')->only('index', 'show');
        $this->middleware('can:empresas.create')->only('create', 'store');
        $this->middleware('can:empresas.edit')->only('edit', 'update');
        $this->middleware('can:empresas.delete')->only('destroy');

    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Empresa::query();

        // busqueda
        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('razon_social', 'like', '%'.$request->input('buscar').'%')
                    ->orWhere('nombre_comercial', 'like', '%'.$request->input('buscar').'%')
                    ->orWhere('numerodocumento', 'like', '%'.$request->input('buscar').'%');
            });
        }

        // filtrar por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $empresas = $query->orderBy('nombre_comercial')
            ->paginate(10)
            ->withQueryString();

        // Obtener estadísticas generales (sin filtros)
        $totalEmpresas = Empresa::count();
        $empresasActivas = Empresa::where('estado', true)->count();

        return view('erp.empresas.index', compact('empresas', 'totalEmpresas', 'empresasActivas'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('erp.empresas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmpresaRequest $request)
    {

        Log::error('Ingresando al crear empresa: ');

        try {

            DB::beginTransaction();

            $empresa = Empresa::create($request->validated());

            // El activity log se registra automáticamente por el trait LogsActivity en el modelo

            DB::commit();

            Log::error('Completado al crear empresa: '.$empresa->id);

            return redirect()->route('empresas.index')->with('success', 'Empresa creada exitosamente.');

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
    public function show(Empresa $empresa)
    {
        // Obtener las actividades relacionadas a esta empresa
        $activities = $empresa->activities()->latest()->get();
        
        return view('erp.empresas.show', compact('empresa', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        return view('erp.empresas.edit', compact('empresa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmpresaRequest $request, Empresa $empresa)
    {
        try {
            DB::beginTransaction();

            $empresa->update($request->validated());

            // El activity log se registra automáticamente por el trait LogsActivity en el modelo

            DB::commit();

            return redirect()
                ->route('empresas.index')
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

            return redirect()
                ->route('empresas.index')
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
