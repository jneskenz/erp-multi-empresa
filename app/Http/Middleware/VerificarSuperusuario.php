<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerificarSuperusuario
{
   /**
    * Verificar que el usuario sea superusuario
    * Solo el superusuario puede acceder al panel /admin
    *
    * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */
   public function handle(Request $request, Closure $next): Response
   {
      $user = $request->user();

      if (!$user || !$user->esSuperusuario()) {
         // Registrar intento de acceso no autorizado
         activity()
            ->causedBy($user)
            ->withProperties([
               'ip' => $request->ip(),
               'ruta' => $request->path(),
               'intento' => 'acceso_panel_admin',
            ])
            ->log('Intento de acceso no autorizado al panel admin');

         abort(403, 'Acceso denegado. Se requieren privilegios de superusuario.');
      }

      // Registrar acceso exitoso
      activity()
         ->causedBy($user)
         ->withProperties([
            'ip' => $request->ip(),
            'ruta' => $request->path(),
         ])
         ->log('Acceso al panel de administración');

      return $next($request);
   }
}
