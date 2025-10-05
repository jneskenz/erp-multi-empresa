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
        
        $this->middleware('can:sedes.view')->only('index', 'show');
        $this->middleware('can:sedes.create')->only('create', 'store');
        $this->middleware('can:sedes.edit')->only('edit', 'update');
        $this->middleware('can:sedes.delete')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sedes = Sede::with('empresa')->get();
        
        return view('erp.sedes.index', compact('sedes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empresas = Empresa::where('estado', 1)->orderBy('razon_social')->get();
        return view('erp.sedes.create', compact('empresas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SedeRequest $request)
    {
        Log::info('Creando nueva sede');

        try {
            DB::beginTransaction();

            $sede = Sede::create($request->validated());

            DB::commit();

            Log::info('Sede creada exitosamente: ' . $sede->id);

            return redirect()->route('sedes.index')->with('success', 'Sede creada exitosamente.');

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
    public function show(Sede $sede)
    {
        // Cargar la relación con empresa
        $sede->load('empresa');
        
        // Obtener otras sedes de la misma empresa (excluyendo la actual)
        $otrasSedesEmpresa = Sede::where('empresa_id', $sede->empresa_id)
            ->where('id', '!=', $sede->id)
            ->orderBy('nombre')
            ->get();

        return view('erp.sedes.show', compact('sede', 'otrasSedesEmpresa'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sede $sede)
    {
        $empresas = Empresa::where('estado', 1)->orderBy('razon_social')->get();
        
        // Obtener otras sedes de la misma empresa (excluyendo la actual)
        $otrasSedesEmpresa = Sede::where('empresa_id', $sede->empresa_id)
            ->where('id', '!=', $sede->id)
            ->orderBy('nombre')
            ->get();
            
        return view('erp.sedes.edit', compact('sede', 'empresas', 'otrasSedesEmpresa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SedeRequest $request, Sede $sede)
    {
        try {
            DB::beginTransaction();

            $sede->update($request->validated());

            DB::commit();

            return redirect()
                ->route('sedes.index')
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
    public function destroy(Sede $sede)
    {
        try {
            DB::beginTransaction();

            $nombreSede = $sede->nombre;
            $sede->delete();

            DB::commit();

            return redirect()
                ->route('sedes.index')
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
