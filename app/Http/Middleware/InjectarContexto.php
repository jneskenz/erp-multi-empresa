<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;

class InjectarContexto
{
   /**
    * Inyectar contexto global en todas las vistas
    *
    * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */
   public function handle(Request $request, Closure $next): Response
   {
      // Si hay usuario autenticado
      if ($user = $request->user()) {

         // Compartir usuario globalmente
         View::share('authUser', $user);

         // Compartir contexto del grupo empresarial
         if ($grupo = $request->get('grupoEmpresa')) {
            View::share('grupoActual', $grupo);
         } elseif ($user->grupoEmpresa) {
            View::share('grupoActual', $user->grupoEmpresa);
         }

         // Compartir contexto de empresa (desde sesión si existe)
         $contextoEmpresa = $request->get('contexto_empresa');
         if (!$contextoEmpresa) {
            $contexto = $user->getContextoActual();
            if ($contexto['empresa_id']) {
               $contextoEmpresa = $user->getEmpresaContexto();
            }
         }
         
         if ($empresa = $request->get('empresa')) {
            View::share('empresaActual', $empresa);
         } elseif ($contextoEmpresa) {
            View::share('empresaActual', $contextoEmpresa);
         } elseif ($user->empresa) {
            View::share('empresaActual', $user->empresa);
         }

         // Compartir contexto de local (desde sesión si existe)
         $contextoLocal = $request->get('contexto_local');
         if (!$contextoLocal) {
            $contexto = $user->getContextoActual();
            if ($contexto['local_id']) {
               $contextoLocal = $user->getLocalContexto();
            }
         }
         
         if ($contextoLocal) {
            View::share('localActual', $contextoLocal);
         } elseif ($user->local) {
            View::share('localActual', $user->local);
         }

         // Compartir empresas con acceso
         $empresasAcceso = $user->getEmpresasConAcceso();
         View::share('empresasConAcceso', $empresasAcceso);

         // Compartir configuración visual
         $configVisual = $user->getConfiguracionVisual();
         View::share('configuracionVisual', $configVisual);

         // Compartir información de roles
         View::share('esSuperusuario', $user->esSuperusuario());
         View::share('esAdministrador', $user->esAdministradorGeneral() || $user->esPropietario());
         View::share('rolPrincipal', $user->rol_principal);
      }

      return $next($request);
   }
}
