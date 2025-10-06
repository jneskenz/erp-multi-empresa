<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\GrupoEmpresa;

class VerificarAccesoGrupo
{
   /**
    * Verificar que el usuario tenga acceso al grupo empresarial
    *
    * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
    */
   public function handle(Request $request, Closure $next): Response
   {
      $user = $request->user();

      // Obtener slug del grupo desde la ruta
      $slugGrupo = $request->route('grupo');

      if (!$slugGrupo) {
         abort(404, 'Grupo empresarial no especificado');
      }

      // Buscar el grupo empresarial
      $grupo = GrupoEmpresa::where('slug', $slugGrupo)->first();

      if (!$grupo) {
         abort(404, 'Grupo empresarial no encontrado');
      }

      // Verificar que el grupo esté activo
      if (!$grupo->estaActivo()) {
         abort(403, 'El grupo empresarial está inactivo');
      }

      // Verificar si el plan ha expirado
      if ($grupo->planExpirado()) {
         return redirect()->route('grupo.plan-expirado', ['grupo' => $grupo->slug])
            ->with('error', 'El plan del grupo empresarial ha expirado');
      }

      // Superusuario tiene acceso total
      if ($user->esSuperusuario()) {
         $request->merge(['grupoEmpresa' => $grupo]);
         return $next($request);
      }

      // Verificar acceso del usuario al grupo
      if (!$user->tieneAccesoAGrupo($grupo->id)) {
         abort(403, 'No tienes acceso a este grupo empresarial');
      }

      // Inyectar el grupo en el request
      $request->merge(['grupoEmpresa' => $grupo]);

      // Registrar acceso en activity log
      activity()
         ->performedOn($grupo)
         ->causedBy($user)
         ->withProperties([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
         ])
         ->log($user->name . ': Accedió al workspace de ' . $grupo->nombre);

      return $next($request);
   }
}
