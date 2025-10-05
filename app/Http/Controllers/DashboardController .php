<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Workspace\Empresa;

class DashboardController extends Controller
{
   /**
    * Constructor
    */
   public function __construct()
   {
      $this->middleware('auth');
   }

   /**
    * Redirigir al dashboard correspondiente según el rol del usuario
    */
   public function index()
   {
      $user = Auth::user();

      // Registrar acceso
      $user->registrarAcceso();

      // SUPERUSUARIO → /admin
      if ($user->esSuperusuario()) {
         return redirect()->route('admin.dashboard');
      }

      // PROPIETARIO o ADMINISTRADOR GENERAL → /{grupo}
      if ($user->esAdministradorGeneral() || $user->esPropietario()) {

         // Verificar que tenga grupo asignado
         if (!$user->grupoEmpresa) {
            // Si es propietario pero sin grupo directo, buscar en relaciones
            $grupoPropietario = $user->gruposActivosComoPropietario()->first();

            if ($grupoPropietario) {
               return redirect()->route('grupo.dashboard', [
                  'grupo' => $grupoPropietario->slug
               ]);
            }

            // Si no tiene grupo, error
            return $this->noTieneGrupoAsignado();
         }

         return redirect()->route('grupo.dashboard', [
            'grupo' => $user->grupoEmpresa->slug
         ]);
      }

      // USUARIO OPERATIVO → /{grupo}/erp/{empresa}
      $empresa = $user->getEmpresaPrincipal();

      if (!$empresa) {
         return $this->noTieneEmpresaAsignada();
      }

      return redirect()->route('empresa.dashboard', [
         'grupo' => $empresa->grupoEmpresa->slug,
         'empresa' => $empresa->slug
      ]);
   }

   /**
    * Cambiar contexto de empresa
    */
   public function cambiarEmpresa(Request $request, Empresa $empresa)
   {
      $user = Auth::user();

      // Verificar que el usuario tenga acceso a la empresa
      if (!$user->tieneAccesoAEmpresa($empresa->id)) {
         return back()->with('error', 'No tienes acceso a esta empresa');
      }

      // Cambiar contexto
      if ($user->cambiarContextoEmpresa($empresa->id)) {

         activity()
            ->causedBy($user)
            ->performedOn($empresa)
            ->log('Cambió a empresa: ' . $empresa->nombre);

         return redirect()->route('empresa.dashboard', [
            'grupo' => $empresa->grupoEmpresa->slug,
            'empresa' => $empresa->slug
         ])->with('success', 'Has cambiado a la empresa: ' . $empresa->nombre);
      }

      return back()->with('error', 'No se pudo cambiar de empresa');
   }

   /**
    * Vista de perfil de usuario
    */
   public function perfil()
   {
      $user = Auth::user();

      return view('perfil.index', compact('user'));
   }

   /**
    * Actualizar perfil de usuario
    */
   public function actualizarPerfil(Request $request)
   {
      $user = Auth::user();

      $validated = $request->validate([
         'name' => 'required|string|max:255',
         'email' => 'required|email|unique:users,email,' . $user->id,
         'telefono' => 'nullable|string|max:20',
         'fecha_nacimiento' => 'nullable|date',
         'preferencias.tema' => 'nullable|in:light,dark',
      ]);

      $user->update($validated);

      activity()
         ->causedBy($user)
         ->log('Actualizó su perfil');

      return back()->with('success', 'Perfil actualizado correctamente');
   }

   /**
    * Actualizar avatar del usuario
    */
   public function actualizarAvatar(Request $request)
   {
      $user = Auth::user();

      $request->validate([
         'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
      ]);

      // Eliminar avatar anterior si existe
      if ($user->avatar) {
         Storage::disk('public')->delete($user->avatar);
      }

      // Guardar nuevo avatar
      $path = $request->file('avatar')->store('avatars', 'public');

      $user->update(['avatar' => $path]);

      activity()
         ->causedBy($user)
         ->log('Actualizó su avatar');

      return back()->with('success', 'Avatar actualizado correctamente');
   }

   /**
    * Vista de notificaciones
    */
   public function notificaciones()
   {
      $user = Auth::user();
      $notificaciones = $user->notifications()->paginate(20);

      return view('notificaciones.index', compact('notificaciones'));
   }

   /**
    * Marcar notificación como leída
    */
   public function marcarLeida($id)
   {
      $user = Auth::user();
      $notificacion = $user->notifications()->findOrFail($id);

      $notificacion->markAsRead();

      return response()->json(['success' => true]);
   }

   /**
    * Error: usuario sin grupo asignado
    */
   private function noTieneGrupoAsignado()
   {
      Auth::logout();

      return redirect()->route('login')
         ->with('error', 'Tu usuario no tiene un grupo empresarial asignado. Contacta al administrador.');
   }

   /**
    * Error: usuario sin empresa asignada
    */
   private function noTieneEmpresaAsignada()
   {
      return view('errors.sin-empresa')
         ->with('error', 'Tu usuario no tiene una empresa asignada. Contacta al administrador del grupo.');
   }
}
