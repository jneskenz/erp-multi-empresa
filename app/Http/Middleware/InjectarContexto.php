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

         // Compartir contexto de empresa
         if ($empresa = $request->get('empresa')) {
            View::share('empresaActual', $empresa);
         } elseif ($user->empresa) {
            View::share('empresaActual', $user->empresa);
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
