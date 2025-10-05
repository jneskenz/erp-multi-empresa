<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Workspace\Empresa;
use Symfony\Component\HttpFoundation\Response;

class VerificarAccesoEmpresa
{
   public function handle(Request $request, Closure $next): Response
   {
      $user = $request->user();

      $slugEmpresa = $request->route('empresa');
      $grupoEmpresa = $request->attributes->get('grupoEmpresa');

      if (!$slugEmpresa) {
         abort(404, 'Empresa no especificada');
      }

      // Buscar empresa activa dentro del grupo
      $empresa = Empresa::where('slug', $slugEmpresa)
         ->where('grupo_empresa_id', $grupoEmpresa->id)
         ->where('is_active', true)
         ->first();

      if (!$empresa) {
         abort(404, 'Empresa no encontrada');
      }

      // Verificar acceso del usuario
      if (!auth()->user()->canAccessEmpresa($empresa->id)) {
         abort(403, 'No tiene acceso a esta empresa');
      }

      // Compartir con vistas
      view()->share('currentEmpresa', $empresa);
      $request->attributes->add(['empresa' => $empresa]);

      return $next($request);
   }
}
