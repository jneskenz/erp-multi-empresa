<?php

namespace App\Http\Controllers\Erp;

use App\Http\Controllers\Controller;
use App\Http\Requests\Erp\LocalStoreRequest;
use App\Http\Requests\Erp\LocalUpdateRequest;
use App\Models\Erp\Local;
use App\Models\Erp\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('can:locales.view')->only('index', 'show');
        $this->middleware('can:locales.create')->only('create', 'store');
        $this->middleware('can:locales.edit')->only('edit', 'update');
        $this->middleware('can:locales.delete')->only('destroy');
        $this->middleware('can:locales.update')->only('update');

    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locales = Local::with('sede')->get();
        return view('erp.locales.index', compact('locales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sedes = Sede::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('erp.locales.create', compact('sedes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LocalStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['codigo'] = strtoupper($data['codigo']);

            $local = Local::create($data);

            DB::commit();

            return redirect()
                ->route('locales.index')
                ->with('success', "Local '{$local->descripcion}' creado correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear el local: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($locale)
    {
        $local = Local::findOrFail($locale);
        $local->load('sede.empresa');
        
        return view('erp.locales.show', compact('local'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($locale)
    {
        $local = Local::findOrFail($locale);
        $sedes = Sede::where('estado', true)
            ->orderBy('nombre')
            ->get();

        return view('erp.locales.edit', compact('local', 'sedes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LocalUpdateRequest $request, $locale)
    {
        try {
            $local = Local::findOrFail($locale);
            
            DB::beginTransaction();

            $data = $request->validated();
            $data['codigo'] = strtoupper($data['codigo']);

            $local->update($data);

            DB::commit();

            return redirect()
                ->route('locales.index')
                ->with('success', "Local '{$local->descripcion}' actualizado correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el local: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($locale)
    {
        try {
            $local = Local::findOrFail($locale);
            $descripcion = $local->descripcion;
            $local->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Local '{$descripcion}' eliminado correctamente."
                ]);
            }

            return redirect()
                ->route('locales.index')
                ->with('success', "Local '{$descripcion}' eliminado correctamente.");

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar el local: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el local: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus($locale)
    {
        try {
            $local = Local::findOrFail($locale);
            $local->update(['estado' => !$local->estado]);
            
            $estado = $local->estado ? 'activado' : 'desactivado';
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Local '{$local->descripcion}' {$estado} correctamente.",
                    'new_status' => $local->estado
                ]);
            }

            return redirect()
                ->back()
                ->with('success', "Local '{$local->descripcion}' {$estado} correctamente.");

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cambiar el estado: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }
}
