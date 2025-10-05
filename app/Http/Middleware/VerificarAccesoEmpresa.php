<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Workspace\Empresa;

class VerificarAccesoEmpresa
{
   /**
    * Verificar que el usuario tenga acceso a la empresa indicada en la ruta
    * 
    * Lógica de acceso según requerimientos:
    * 1. Superusuario: acceso total a todas las empresas
    * 2. Administrador_general/Propietario: acceso a todas las empresas de su grupo
    * 3. Usuarios operativos: solo acceso a empresas específicamente asignadas
    * 
    * Rutas válidas: /{grupo}/erp/{empresa}
    *
    * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */
   public function handle(Request $request, Closure $next): Response
   {
      $user = $request->user();

      // Verificar autenticación
      if (!$user) {
         return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder.');
      }

      // Obtener slug de la empresa desde la ruta
      $slugEmpresa = $request->route('empresa');

      if (!$slugEmpresa) {
         abort(404, 'Empresa no especificada en la ruta');
      }

      // Obtener el grupo empresarial del request (debe ser inyectado por grupo.access middleware)
      $grupo = $request->get('grupoEmpresa');

      if (!$grupo) {
         abort(500, 'Contexto de grupo empresarial no encontrado. Verifica el middleware grupo.access');
      }

      // Buscar la empresa por slug dentro del grupo
      $empresa = Empresa::where('slug', $slugEmpresa)
         ->where('grupo_empresa_id', $grupo->id)
         ->first();

      if (!$empresa) {
         abort(404, 'Empresa no encontrada en este grupo empresarial');
      }

      // Verificar que la empresa esté activa
      if (method_exists($empresa, 'estaActiva') && !$empresa->estaActiva()) {
         return redirect()->route('grupo.dashboard', ['grupo' => $grupo->slug])
            ->with('error', 'La empresa está inactiva. Contacta con el administrador del grupo.');
      }

      // ==================== VERIFICACIÓN DE ACCESO ====================

      // 1. SUPERUSUARIO: Acceso total (puede ver cualquier empresa)
      if ($user->esSuperusuario()) {
         $request->merge(['empresa' => $empresa]);

         // Log de acceso del superusuario
         activity()
            ->performedOn($empresa)
            ->causedBy($user)
            ->withProperties([
               'ip' => $request->ip(),
               'user_agent' => $request->userAgent(),
               'tipo_acceso' => 'superusuario',
               'grupo_id' => $grupo->id,
            ])
            ->log('Superusuario accedió a la empresa');

         return $next($request);
      }

      // Verificar que el usuario pertenezca al mismo grupo empresarial
      if ($user->grupo_empresa_id !== $empresa->grupo_empresa_id) {
         // Registrar intento de acceso entre grupos
         activity()
            ->causedBy($user)
            ->withProperties([
               'ip' => $request->ip(),
               'grupo_usuario' => $user->grupo_empresa_id,
               'grupo_empresa_intentada' => $empresa->grupo_empresa_id,
               'empresa_intentada' => $empresa->id,
            ])
            ->log('Intento de acceso a empresa de otro grupo empresarial');

         abort(403, 'No tienes acceso a empresas de otros grupos empresariales');
      }

      // 2. ADMINISTRADOR GENERAL o PROPIETARIO: Acceso a todas las empresas de su grupo
      if ($user->esAdministradorGeneral() || $user->esPropietario()) {
         $request->merge(['empresa' => $empresa]);

         // Log de acceso del administrador
         activity()
            ->performedOn($empresa)
            ->causedBy($user)
            ->withProperties([
               'ip' => $request->ip(),
               'user_agent' => $request->userAgent(),
               'tipo_acceso' => 'administrador_grupo',
               'rol' => $user->rol_principal,
            ])
            ->log('Administrador del grupo accedió a la empresa');

         return $next($request);
      }

      // 3. USUARIOS OPERATIVOS: Solo acceso a empresas asignadas
      if (!$user->tieneAccesoAEmpresa($empresa->id)) {
         // Obtener la empresa principal del usuario
         $empresaPrincipal = $user->getEmpresaPrincipal();

         // Registrar intento de acceso no autorizado
         activity()
            ->performedOn($empresa)
            ->causedBy($user)
            ->withProperties([
               'ip' => $request->ip(),
               'empresa_intentada' => $empresa->id,
               'empresa_asignada' => $empresaPrincipal?->id,
               'rol' => $user->rol_principal,
            ])
            ->log('Intento de acceso a empresa sin permisos asignados');

         // Si tiene otra empresa asignada, redirigir a su dashboard
         if ($empresaPrincipal) {
            return redirect()->route('empresa.dashboard', [
               'grupo' => $empresaPrincipal->grupoEmpresa->slug,
               'empresa' => $empresaPrincipal->slug
            ])->with('error', 'No tienes acceso a esta empresa. Has sido redirigido a tu empresa asignada.');
         }

         // Si no tiene ninguna empresa asignada
         return redirect()->route('grupo.dashboard', ['grupo' => $grupo->slug])
            ->with('error', 'No tienes acceso a esta empresa y no tienes una empresa asignada.');
      }

      // Acceso concedido: inyectar empresa en el request
      $request->merge(['empresa' => $empresa]);

      // Registrar acceso exitoso del usuario operativo
      activity()
         ->performedOn($empresa)
         ->causedBy($user)
         ->withProperties([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'tipo_acceso' => 'usuario_operativo',
            'rol' => $user->rol_principal,
         ])
         ->log('Usuario operativo accedió a su empresa asignada');

      return $next($request);
   }
}
