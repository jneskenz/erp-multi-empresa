<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarRolAdministrador
{
   /**
    * Verificar que el usuario tenga rol de administrador
    * 
    * Lógica según requerimientos:
    * 
    * Para rutas de GRUPO (/{grupo}/*):
    * - Solo: superusuario, propietario, administrador_general
    * - Usuarios operativos NO tienen acceso
    * 
    * Para rutas de EMPRESA (/{grupo}/erp/{empresa}/*) con este middleware:
    * - Superusuario
    * - Propietario del grupo
    * - Administrador_general del grupo
    * - Administrador_empresa (solo de esa empresa específica)
    * 
    * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */
   public function handle(Request $request, Closure $next): Response
   {
      $user = $request->user();

      // Verificar autenticación
      if (!$user) {
         return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
      }

      // Obtener contexto del request (inyectado por middlewares anteriores)
      $grupo = $request->get('grupoEmpresa');
      $empresa = $request->get('empresa');

      // ==================== 1. SUPERUSUARIO ====================
      // El superusuario siempre tiene acceso a todo
      if ($user->esSuperusuario()) {
         return $next($request);
      }

      // ==================== 2. CONTEXTO DE GRUPO ====================
      // Rutas: /{grupo}/*
      // Solo administrador_general o propietario pueden acceder
      if ($grupo && !$empresa) {
         // Verificar que sea propietario o administrador general del grupo
         if ($user->puedeGestionarGrupo($grupo->id)) {
            return $next($request);
         }

         // Registrar intento de acceso no autorizado al grupo
         activity()
            ->causedBy($user)
            ->withProperties([
               'ip' => $request->ip(),
               'ruta' => $request->path(),
               'grupo_id' => $grupo->id,
               'rol_usuario' => $user->rol_principal,
               'motivo' => 'Usuario operativo intentó acceder a panel de grupo',
            ])
            ->log('Intento de acceso no autorizado: se requiere rol administrador del grupo');

         // Si el usuario es operativo, redirigir a su empresa
         $empresaPrincipal = $user->getEmpresaPrincipal();
         if ($empresaPrincipal) {
            return redirect()->route('empresa.dashboard', [
               'grupo' => $empresaPrincipal->grupoEmpresa->slug,
               'empresa' => $empresaPrincipal->slug
            ])->with('error', 'No tienes permisos para acceder al panel de administración del grupo. Redirigido a tu empresa.');
         }

         abort(403, 'Acceso denegado. Solo administradores del grupo pueden acceder a esta sección.');
      }

      // ==================== 3. CONTEXTO DE EMPRESA ====================
      // Rutas: /{grupo}/erp/{empresa}/* (con middleware rol.administrador)
      // Pueden acceder: administrador_general, propietario, administrador_empresa
      if ($empresa) {
         // Verificar si es administrador general o propietario del grupo
         if ($user->esAdministradorGeneral() || $user->esPropietario()) {
            if ($user->puedeGestionarGrupo($empresa->grupo_empresa_id)) {
               return $next($request);
            }
         }

         // Verificar si es administrador de esta empresa específica
         if ($user->hasRole('administrador_empresa')) {
            // Debe tener acceso a esta empresa específica
            if ($user->tieneAccesoAEmpresa($empresa->id)) {
               return $next($request);
            }
         }

         // Registrar intento de acceso no autorizado a funciones de administrador
         activity()
            ->performedOn($empresa)
            ->causedBy($user)
            ->withProperties([
               'ip' => $request->ip(),
               'ruta' => $request->path(),
               'empresa_id' => $empresa->id,
               'rol_usuario' => $user->rol_principal,
               'motivo' => 'Usuario sin rol administrador intentó acceder a función administrativa',
            ])
            ->log('Intento de acceso no autorizado: se requiere rol administrador de empresa');

         abort(403, 'Acceso denegado. Se requieren privilegios de administrador para esta acción.');
      }

      // ==================== 4. SIN CONTEXTO ====================
      // No hay grupo ni empresa en el contexto, registrar y denegar
      activity()
         ->causedBy($user)
         ->withProperties([
            'ip' => $request->ip(),
            'ruta' => $request->path(),
            'motivo' => 'Middleware invocado sin contexto de grupo o empresa',
         ])
         ->log('Error en verificación de rol: contexto no disponible');

      abort(500, 'Error en la verificación de permisos. Contexto no disponible.');
   }
}
