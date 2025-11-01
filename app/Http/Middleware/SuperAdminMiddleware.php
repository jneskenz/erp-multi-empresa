<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si el usuario está autenticado
        if (!$request->user()) {
            Log::warning('Acceso denegado: usuario no autenticado intenta acceder a ruta de superadministrador.');
            return redirect()->route('login')->with('error', 'Debes iniciar sesión.');
        }

        // Verificar si es superadministrador
        if (!$request->user()->isSuperAdmin()) {
            Log::warning('Acceso denegado: usuario ' . $request->user()->id . ' no es superadministrador.');
            abort(403, 'Acceso denegado. Solo superadministradores pueden acceder a esta sección.');
        }

        return $next($request);
    }
}
