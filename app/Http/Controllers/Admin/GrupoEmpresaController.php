<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrupoEmpresa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class GrupoEmpresaController extends Controller
{
   /**
    * Listar todos los grupos empresariales
    */
   public function index(Request $request)
   {
      $query = GrupoEmpresa::with('propietarios');

      // Filtros
      if ($request->filled('search')) {
         $search = $request->search;
         $query->where(function ($q) use ($search) {
            $q->where('nombre', 'like', "%{$search}%")
               ->orWhere('slug', 'like', "%{$search}%")
               ->orWhere('ruc', 'like', "%{$search}%");
         });
      }

      if ($request->filled('plan')) {
         $query->where('plan_actual', $request->plan);
      }

      if ($request->filled('activo')) {
         $query->where('activo', $request->activo);
      }

      $grupos = $query->latest()->paginate(20);

      return view('apps.admin.grupos.index', compact('grupos'));
   }

   /**
    * Mostrar formulario de creación
    */
   public function create()
   {
      $planes = ['gratuito', 'basico', 'profesional', 'enterprise'];
      $paises = ['Perú', 'Colombia', 'Chile', 'México', 'Argentina'];

      return view('apps.admin.grupos.create', compact('planes', 'paises'));
   }

   /**
    * Crear nuevo grupo empresarial
    */
   public function store(Request $request)
   {
      $validated = $request->validate([
         'nombre' => 'required|string|max:255',
         'ruc' => 'nullable|string|max:20|unique:grupo_empresas,ruc',
         'razon_social' => 'nullable|string|max:255',
         'email' => 'required|email|unique:grupo_empresas,email',
         'telefono' => 'nullable|string|max:20',
         'pais' => 'required|string',
         'plan_actual' => 'required|string',
         'max_empresas' => 'required|integer|min:1',
         'max_usuarios' => 'required|integer|min:1',
         'fecha_inicio_plan' => 'required|date',
         'fecha_fin_plan' => 'required|date|after:fecha_inicio_plan',
      ]);

      // Generar slug único
      $validated['slug'] = Str::slug($validated['nombre']);
      $originalSlug = $validated['slug'];
      $count = 1;

      while (GrupoEmpresa::where('slug', $validated['slug'])->exists()) {
         $validated['slug'] = $originalSlug . '-' . $count;
         $count++;
      }

      $validated['activo'] = true;

      $grupo = GrupoEmpresa::create($validated);

      // Registrar en activity log
      activity()
         ->performedOn($grupo)
         ->causedBy(Auth::user())
         ->withProperties($validated)
         ->log('Creó nuevo grupo empresarial');

      return redirect()
         ->route('admin.grupos.show', $grupo)
         ->with('success', 'Grupo empresarial creado correctamente.');
   }

   /**
    * Mostrar detalle del grupo
    */
   public function show(GrupoEmpresa $grupo)
   {
      $grupo->load('propietarios', 'empresas', 'usuarios');

      // Estadísticas del grupo
      $stats = [
         'total_empresas' => $grupo->empresas()->count(),
         'empresas_activas' => $grupo->empresas()->where('activo', true)->count(),
         'total_usuarios' => $grupo->usuarios()->count(),
         'usuarios_activos' => $grupo->usuarios()->where('activo', true)->count(),
      ];

      // Actividad reciente del grupo
      $actividad = activity()
         ->inLog('default')
         ->where('subject_type', GrupoEmpresa::class)
         ->where('subject_id', $grupo->id)
         ->latest()
         ->take(20)
         ->get();

      return view('apps.admin.grupos.show', compact('grupo', 'stats', 'actividad'));
   }

   /**
    * Mostrar formulario de edición
    */
   public function edit(GrupoEmpresa $grupo)
   {
      $planes = ['gratuito', 'basico', 'profesional', 'enterprise'];
      $paises = ['Perú', 'Colombia', 'Chile', 'México', 'Argentina'];

      return view('apps.admin.grupos.edit', compact('grupo', 'planes', 'paises'));
   }

   /**
    * Actualizar grupo empresarial
    */
   public function update(Request $request, GrupoEmpresa $grupo)
   {
      $validated = $request->validate([
         'nombre' => 'required|string|max:255',
         'ruc' => 'nullable|string|max:20|unique:grupo_empresas,ruc,' . $grupo->id,
         'razon_social' => 'nullable|string|max:255',
         'email' => 'required|email|unique:grupo_empresas,email,' . $grupo->id,
         'telefono' => 'nullable|string|max:20',
         'pais' => 'required|string',
         'direccion' => 'nullable|string',
         'max_empresas' => 'required|integer|min:1',
         'max_usuarios' => 'required|integer|min:1',
      ]);

      $grupo->update($validated);

      activity()
         ->performedOn($grupo)
         ->causedBy(Auth::user())
         ->withProperties([
            'old' => $grupo->getOriginal(),
            'new' => $validated
         ])
         ->log('Actualizó grupo empresarial');

      return redirect()
         ->route('admin.grupos.show', $grupo)
         ->with('success', 'Grupo empresarial actualizado correctamente.');
   }

   /**
    * Eliminar grupo empresarial (soft delete)
    */
   public function destroy(GrupoEmpresa $grupo)
   {
      // Verificar si tiene empresas activas
      if ($grupo->empresas()->where('activo', true)->count() > 0) {
         return redirect()
            ->back()
            ->with('error', 'No se puede eliminar un grupo con empresas activas.');
      }

      activity()
         ->performedOn($grupo)
         ->causedBy(Auth::user())
         ->log('Eliminó grupo empresarial');

      $grupo->delete();

      return redirect()
         ->route('admin.grupos.index')
         ->with('success', 'Grupo empresarial eliminado correctamente.');
   }

   /**
    * Activar grupo empresarial
    */
   public function activar(GrupoEmpresa $grupo)
   {
      $grupo->update(['activo' => true]);

      activity()
         ->performedOn($grupo)
         ->causedBy(Auth::user())
         ->log('Activó grupo empresarial');

      return redirect()
         ->back()
         ->with('success', 'Grupo empresarial activado correctamente.');
   }

   /**
    * Suspender grupo empresarial
    */
   public function suspender(GrupoEmpresa $grupo)
   {
      $grupo->update(['activo' => false]);

      activity()
         ->performedOn($grupo)
         ->causedBy(Auth::user())
         ->log('Suspendió grupo empresarial');

      return redirect()
         ->back()
         ->with('success', 'Grupo empresarial suspendido correctamente.');
   }

   /**
    * Cambiar plan del grupo
    */
   public function cambiarPlan(Request $request, GrupoEmpresa $grupo)
   {
      $validated = $request->validate([
         'plan_actual' => 'required|string|in:gratuito,basico,profesional,enterprise',
         'max_empresas' => 'required|integer|min:1',
         'max_usuarios' => 'required|integer|min:1',
         'fecha_inicio_plan' => 'required|date',
         'fecha_fin_plan' => 'required|date|after:fecha_inicio_plan',
         'modulos_disponibles' => 'nullable|array',
      ]);

      $grupo->update($validated);

      activity()
         ->performedOn($grupo)
         ->causedBy(Auth::user())
         ->withProperties([
            'plan_anterior' => $grupo->getOriginal('plan_actual'),
            'plan_nuevo' => $validated['plan_actual']
         ])
         ->log('Cambió plan del grupo empresarial');

      return redirect()
         ->back()
         ->with('success', 'Plan actualizado correctamente.');
   }
}
