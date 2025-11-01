<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\LeadCliente;
use App\Models\Admin\GrupoEmpresarial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\FacadesLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class LeadClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = LeadCliente::query();

        // busquedas 
        if($request->filled('search')){
            $search = $request->search;
            $query->where(function($q) use ($search){
                $q->where('empresa', 'like', '%'.($search).'%')
                    ->orWhere('ruc', 'like', '%'.($search).'%')
                  ->orWhere('correo', 'like', '%'.($search).'%')
                  ->orWhere('rubro_empresa', 'like', '%'.($search).'%')
                  ->orWhere('cliente', 'like', '%'.($search).'%')
                  ->orWhere('nro_documento', 'like', '%'.($search).'%')
                  ->orWhere('correo', 'like', '%'.($search).'%')
                  ->orWhere('telefono', 'like', '%'.($search).'%');
            });
        }

        // paginacion
        $perPage = $request->get('per_page', 10);

        if(!in_array($perPage, [10, 25, 50, 100])){
            $perPage = 10;
        }

        $leads = $query->paginate($perPage)->withQueryString();

        return view('admin.lead-cliente.index', compact('leads'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.lead-cliente.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Datos de la empresa
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:50|unique:lead_clientes,ruc',
            'rubro_empresa' => 'nullable|string|max:100',
            'nro_personal' => 'nullable|integer|min:1',
            'pais_origen' => 'nullable|string|max:100',
            'wizard_descripcion' => 'nullable|string|max:1000',
            
            // Datos personales
            'wizard_representante_nombre' => 'required|string|max:255',
            'wizard_representante_dni' => 'nullable|string|max:20',
            'wizard_representante_telefono' => 'nullable|string|max:20',
            'wizard_representante_email' => 'required|email|max:255',
            'cargoempresa' => 'nullable|string|max:100',
            
            // Plan de suscripción
            'plan_suscripcion' => 'required|in:demo,basico,profesional,empresarial',
        ], [
            'nombre.required' => 'El nombre de la empresa es obligatorio.',
            'codigo.required' => 'El código RUC/NIT es obligatorio.',
            'codigo.unique' => 'Este RUC/NIT ya está registrado en el sistema.',
            'wizard_representante_nombre.required' => 'El nombre del representante es obligatorio.',
            'wizard_representante_email.required' => 'El email del representante es obligatorio.',
            'wizard_representante_email.email' => 'El email debe tener un formato válido.',
            'plan_suscripcion.required' => 'Debe seleccionar un plan de suscripción.',
        ]);

        try {
            $leadCliente = LeadCliente::create([
                'empresa' => $request->nombre,
                'ruc' => $request->codigo,
                'rubro_empresa' => $request->rubro_empresa,
                'nro_empleados' => $request->nro_personal,
                'pais' => $request->pais_origen,
                'descripcion' => $request->wizard_descripcion,
                'cliente' => $request->wizard_representante_nombre,
                'nro_documento' => $request->wizard_representante_dni,
                'correo' => $request->wizard_representante_email,
                'telefono' => $request->wizard_representante_telefono,
                'cargo' => $request->cargoempresa,
                'plan_interes' => $request->plan_suscripcion,
                'estado' => '1'
            ]);

            // Log de actividad
            Log::info('Nuevo lead cliente registrado', [
                'lead_id' => $leadCliente->id,
                'empresa' => $leadCliente->empresa,
                'plan' => $leadCliente->plan_interes,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Excelente! Tu registro ha sido completado exitosamente.',
                'data' => [
                    'lead_id' => $leadCliente->id,
                    'empresa' => $leadCliente->empresa,
                    'plan' => $leadCliente->plan_interes,
                    'is_demo' => $leadCliente->plan_interes === 'demo'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al registrar lead cliente', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar tu registro. Por favor, inténtalo nuevamente.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LeadCliente $leadCliente)
    {
        return view('admin.lead-cliente.show', compact('leadCliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeadCliente $leadCliente)
    {
        return view('admin.lead-cliente.edit', compact('leadCliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeadCliente $leadCliente)
    {
        $request->validate([
            'empresa' => 'required|string|max:255',
            'ruc' => 'required|string|max:50|unique:lead_clientes,ruc,' . $leadCliente->id,
            'rubro_empresa' => 'nullable|string|max:100',
            'nro_empleados' => 'nullable|integer|min:1',
            'pais' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:1000',
            'cliente' => 'required|string|max:255',
            'nro_documento' => 'nullable|string|max:20',
            'correo' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'cargo' => 'nullable|string|max:100',
            'plan_interes' => 'required|in:demo,basico,profesional,empresarial',
            'estado' => 'boolean',
        ], [
            'empresa.required' => 'El nombre de la empresa es obligatorio.',
            'ruc.required' => 'El código RUC/NIT es obligatorio.',
            'ruc.unique' => 'Este RUC/NIT ya está registrado en el sistema.',
            'cliente.required' => 'El nombre del cliente es obligatorio.',
            'correo.required' => 'El email es obligatorio.',
            'correo.email' => 'El email debe tener un formato válido.',
            'plan_interes.required' => 'Debe seleccionar un plan de interés.',
        ]);

        try {
            $leadCliente->update([
                'empresa' => $request->empresa,
                'ruc' => $request->ruc,
                'rubro_empresa' => $request->rubro_empresa,
                'nro_empleados' => $request->nro_empleados,
                'pais' => $request->pais,
                'descripcion' => $request->descripcion,
                'cliente' => $request->cliente,
                'nro_documento' => $request->nro_documento,
                'correo' => $request->correo,
                'telefono' => $request->telefono,
                'cargo' => $request->cargo,
                'plan_interes' => $request->plan_interes,
                'estado' => $request->has('estado') ? 1 : 0,
            ]);

            // Log de actividad
            Log::info('Lead cliente actualizado', [
                'lead_id' => $leadCliente->id,
                'empresa' => $leadCliente->empresa,
                'ip' => $request->ip()
            ]);

            return redirect()->route('admin.lead-cliente.index')
                           ->with('success', 'Lead cliente actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al actualizar lead cliente', [
                'lead_id' => $leadCliente->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return back()->withInput()
                        ->with('error', 'Ocurrió un error al actualizar el lead cliente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeadCliente $leadCliente)
    {
        try {
            $empresa = $leadCliente->empresa;
            $leadId = $leadCliente->id;
            
            $leadCliente->delete();

            // Log de actividad
            Log::info('Lead cliente eliminado', [
                'lead_id' => $leadId,
                'empresa' => $empresa,
                'ip' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead cliente eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar lead cliente', [
                'lead_id' => $leadCliente->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el lead cliente.'
            ], 500);
        }
    }

    /**
     * Dar de alta a un lead cliente
     */
    public function darDeAlta(LeadCliente $leadCliente)
    {
        try {
            DB::beginTransaction();

            // Verificar si ya existe un grupo empresarial con el mismo RUC
            $grupoExistente = GrupoEmpresarial::where('codigo', $leadCliente->ruc)->first();
            if ($grupoExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un grupo empresarial con este RUC/NIT.'
                ], 422);
            }

            // Verificar si ya existe un usuario con el mismo email
            $usuarioExistente = User::where('email', $leadCliente->correo)->first();
            if ($usuarioExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un usuario registrado con este email.'
                ], 422);
            }

            // Crear usuario
            $user = User::create([
                'name' => $leadCliente->cliente,
                'email' => $leadCliente->correo,
                'password' => Hash::make('demo2025'), // Contraseña temporal
                'is_super_admin' => false
            ]);

            // Asignar rol según el plan de interés
            $roleName = $this->getRolePorPlan($leadCliente->plan_interes);
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->assignRole($role);
            }

            // Crear grupo empresarial
            $grupoEmpresarial = GrupoEmpresarial::create([
                'user_uuid' => $user->id,
                'nombre' => $leadCliente->empresa,
                'descripcion' => $leadCliente->descripcion,
                'codigo' => $leadCliente->ruc,
                'pais_origen' => $leadCliente->pais,
                'telefono' => $leadCliente->telefono,
                'email' => $leadCliente->correo,
                'sitio_web' => null,
                'direccion_matriz' => null,
                'estado' => true
            ]);

            // Actualizar estado del lead cliente
            $leadCliente->update([
                'estado' => true // Marcar como procesado/activo
            ]);

            DB::commit();

            // Log de actividad
            Log::info('Lead cliente dado de alta exitosamente', [
                'lead_id' => $leadCliente->id,
                'empresa' => $leadCliente->empresa,
                'grupo_empresarial_id' => $grupoEmpresarial->id,
                'user_id' => $user->id,
                'plan' => $leadCliente->plan_interes,
                'rol_asignado' => $roleName,
                'ip' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead cliente dado de alta exitosamente. Se ha creado el grupo empresarial y usuario correspondiente.',
                'data' => [
                    'grupo_empresarial_id' => $grupoEmpresarial->id,
                    'user_id' => $user->id,
                    'empresa' => $grupoEmpresarial->nombre,
                    'usuario_email' => $user->email,
                    'rol_asignado' => $roleName,
                    'password' => 'demo2025',
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al dar de alta lead cliente', [
                'lead_id' => $leadCliente->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al dar de alta el lead cliente. ' . ($e->getMessage() ?? 'Error desconocido')
            ], 500);
        }
    }

    /**
     * Obtener el rol correspondiente según el plan de interés
     */
    private function getRolePorPlan($planInteres)
    {
        $rolesMap = [
            'demo' => 'cliente_demo',
            'basico' => 'cliente_basico', 
            'profesional' => 'cliente_profesional',
            'empresarial' => 'cliente_empresarial'
        ];

        return $rolesMap[$planInteres] ?? 'cliente_basico';
    }
}
